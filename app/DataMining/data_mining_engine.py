import logging
from datetime import datetime

import numpy as np
import pandas as pd
from dotenv import dotenv_values
from mlxtend.frequent_patterns import apriori, association_rules
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
from sqlalchemy import create_engine, text

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s %(levelname)s %(message)s')
logger = logging.getLogger('data-mining-engine')

CONFIG = dotenv_values('.env')

DB_USER = CONFIG.get('DB_USERNAME', 'root')
DB_PASSWORD = CONFIG.get('DB_PASSWORD', '')
DB_HOST = CONFIG.get('DB_HOST', '127.0.0.1')
DB_PORT = CONFIG.get('DB_PORT', '3306')
DB_NAME = CONFIG.get('DB_DATABASE', 'e-commerce_dataset')

CONNECTION_STRING = (
    f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}?charset=utf8mb4"
)


def get_engine():
    return create_engine(CONNECTION_STRING, pool_pre_ping=True)


def load_data(engine):
    with engine.connect() as conn:
        logger.info('Loading raw orders and items from database')
        orders = pd.read_sql(text('SELECT order_id, customer_id, order_status, order_purchase_timestamp FROM orders'), conn)
        order_items = pd.read_sql(text('SELECT order_id, product_id, price, freight_value FROM order_items'), conn)
        customers = pd.read_sql(text('SELECT customer_id, customer_unique_id FROM customers'), conn)
    return orders, order_items, customers


def build_rfm(orders: pd.DataFrame, order_items: pd.DataFrame, customers: pd.DataFrame):
    logger.info('Build RFM table for customers')

    orders = orders.dropna(subset=['order_purchase_timestamp'])
    orders['order_purchase_timestamp'] = pd.to_datetime(orders['order_purchase_timestamp'])

    order_totals = (
        order_items
        .groupby('order_id', as_index=False)
        .agg(total_price=('price', 'sum'), total_freight=('freight_value', 'sum'))
    )

    order_data = orders.merge(order_totals, on='order_id', how='left')
    reference_date = order_data['order_purchase_timestamp'].max() + pd.Timedelta(days=1)

    customer_rfm = (
        order_data.groupby('customer_id', as_index=False)
        .agg(
            recency_days=('order_purchase_timestamp', lambda x: (reference_date - x.max()).days),
            frequency=('order_id', 'nunique'),
            monetary=('total_price', 'sum'),
        )
    )
    customer_rfm['monetary'] = customer_rfm['monetary'].fillna(0.0)

    customer_rfm = customer_rfm.merge(customers[['customer_id', 'customer_unique_id']], on='customer_id', how='left')
    customer_rfm = customer_rfm.dropna(subset=['customer_unique_id'])

    logger.info('Standardizing RFM for clustering')
    scaler = StandardScaler()
    features = scaler.fit_transform(customer_rfm[['recency_days', 'frequency', 'monetary']])

    kmeans = KMeans(n_clusters=4, random_state=42, n_init=10)
    customer_rfm['cluster'] = kmeans.fit_predict(features)

    logger.info('Mapping clustering labels to customer segments')
    cluster_summary = (
        customer_rfm.groupby('cluster')
        .agg(
            avg_recency=('recency_days', 'mean'),
            avg_frequency=('frequency', 'mean'),
            avg_monetary=('monetary', 'mean')
        )
        .reset_index()
    )

    cluster_summary['score'] = (
        -cluster_summary['avg_recency'].rank(ascending=False) +
        cluster_summary['avg_frequency'].rank(ascending=True) +
        cluster_summary['avg_monetary'].rank(ascending=True)
    )
    cluster_summary = cluster_summary.sort_values('score', ascending=False)

    labels = ['Champions', 'Loyal', 'At Risk', 'Hibernating']
    cluster_order = dict(zip(cluster_summary['cluster'].tolist(), labels))
    customer_rfm['segment_label'] = customer_rfm['cluster'].map(cluster_order)

    result = customer_rfm[['customer_unique_id', 'segment_label', 'recency_days', 'frequency', 'monetary']].rename(
        columns={'recency_days': 'recency'}
    )
    return result


def save_customer_segments(engine, customer_segments: pd.DataFrame):
    logger.info('Writing customer segments to database')
    customer_segments['updated_at'] = datetime.utcnow()
    with engine.begin() as conn:
        conn.execute(text('TRUNCATE TABLE customer_segments'))
        customer_segments.to_sql('customer_segments', conn, if_exists='append', index=False)


def build_product_recommendations(order_items: pd.DataFrame):
    logger.info('Building transaction basket matrix')
    transactions = order_items.groupby('order_id')['product_id'].apply(list).tolist()
    unique_products = sorted(set(order_items['product_id'].tolist()))

    te = pd.DataFrame(
        [{product: (product in transaction) for product in unique_products} for transaction in transactions]
    )

    logger.info('Running Apriori algorithm')
    frequent_itemsets = apriori(te, min_support=0.02, use_colnames=True)
    if frequent_itemsets.empty:
        logger.warning('No frequent itemsets found with min_support=0.02')
        return pd.DataFrame(columns=['product_id', 'recommended_product_id', 'confidence', 'support'])

    rules = association_rules(frequent_itemsets, metric='confidence', min_threshold=0.5)
    if rules.empty:
        logger.warning('No association rules found with confidence >= 0.5')
        return pd.DataFrame(columns=['product_id', 'recommended_product_id', 'confidence', 'support'])

    rules = rules.assign(
        antecedent=lambda df: df['antecedents'].apply(lambda x: sorted(list(x))),
        consequent=lambda df: df['consequents'].apply(lambda x: sorted(list(x)))
    )

    rows = []
    for _, row in rules.iterrows():
        for antecedent in row['antecedent']:
            for consequent in row['consequent']:
                rows.append({
                    'product_id': antecedent,
                    'recommended_product_id': consequent,
                    'confidence': float(row['confidence']),
                    'support': float(row['support']),
                })

    recommendations = pd.DataFrame(rows)
    recommendations = (
        recommendations.sort_values(['product_id', 'confidence', 'support'], ascending=[True, False, False])
        .drop_duplicates(['product_id', 'recommended_product_id'])
    )

    final = (
        recommendations.groupby('product_id')
        .head(3)
        .reset_index(drop=True)
    )
    return final


def save_product_recommendations(engine, recommendations: pd.DataFrame):
    logger.info('Writing product recommendations to database')
    if recommendations.empty:
        logger.warning('Product recommendation table is empty; skipping insert.')
        return
    recommendations['updated_at'] = datetime.utcnow()
    with engine.begin() as conn:
        conn.execute(text('TRUNCATE TABLE product_recommendations'))
        recommendations.to_sql('product_recommendations', conn, if_exists='append', index=False)


def run():
    logger.info('Starting data mining engine')
    engine = get_engine()

    orders, order_items, customers = load_data(engine)
    customer_segments = build_rfm(orders, order_items, customers)
    save_customer_segments(engine, customer_segments)

    product_recommendations = build_product_recommendations(order_items)
    save_product_recommendations(engine, product_recommendations)

    logger.info('Data mining engine completed successfully')


if __name__ == '__main__':
    try:
        run()
    except Exception as exc:
        logger.exception('Data mining script failed: %s', exc)
        raise

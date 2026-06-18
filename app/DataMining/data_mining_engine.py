import logging
import sys
from datetime import datetime
from pathlib import Path
from urllib.parse import quote_plus

import numpy as np
import pandas as pd
from dotenv import dotenv_values
from mlxtend.frequent_patterns import apriori, association_rules
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
from sqlalchemy import create_engine, text

# ---------------------------------------------------------------------------
# Logging
# ---------------------------------------------------------------------------
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s  %(levelname)-8s  %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
    handlers=[logging.StreamHandler(sys.stdout)],
)
logger = logging.getLogger('data-mining-engine')

# ---------------------------------------------------------------------------
# Configuration — read .env from project root (2 levels up from this file)
# ---------------------------------------------------------------------------
_HERE = Path(__file__).resolve()
_PROJECT_ROOT = _HERE.parent.parent.parent  # app/DataMining/ → project root

CONFIG = dotenv_values(_PROJECT_ROOT / '.env')

_DB_USER = CONFIG.get('DB_USERNAME', 'root')
_DB_PASS = CONFIG.get('DB_PASSWORD', '')
_DB_HOST = CONFIG.get('DB_HOST', '127.0.0.1')
_DB_PORT = CONFIG.get('DB_PORT', '3306')
_DB_NAME = CONFIG.get('DB_DATABASE', 'laravel')

# URL-encode credentials to handle special characters safely
_CONNECTION_STRING = (
    f"mysql+pymysql://{quote_plus(_DB_USER)}:{quote_plus(_DB_PASS)}"
    f"@{_DB_HOST}:{_DB_PORT}/{quote_plus(_DB_NAME)}?charset=utf8mb4"
)


# ---------------------------------------------------------------------------
# Database helpers
# ---------------------------------------------------------------------------
def get_engine():
    return create_engine(_CONNECTION_STRING, pool_pre_ping=True)


def load_data(engine):
    logger.info('Loading orders, order_items, and customers from database…')
    with engine.connect() as conn:
        orders = pd.read_sql(
            text(
                'SELECT order_id, customer_id, order_status, order_purchase_timestamp '
                'FROM orders'
            ),
            conn,
        )
        order_items = pd.read_sql(
            text('SELECT order_id, product_id, price, freight_value FROM order_items'),
            conn,
        )
        customers = pd.read_sql(
            text('SELECT customer_id, customer_unique_id FROM customers'),
            conn,
        )
    logger.info(
        f'Loaded {len(orders):,} orders | {len(order_items):,} items | {len(customers):,} customers'
    )
    return orders, order_items, customers


# ---------------------------------------------------------------------------
# K-Means Clustering — RFM Segmentation
# ---------------------------------------------------------------------------
def build_rfm(
    orders: pd.DataFrame,
    order_items: pd.DataFrame,
    customers: pd.DataFrame,
) -> pd.DataFrame:
    logger.info('Building RFM table…')

    orders = orders.dropna(subset=['order_purchase_timestamp']).copy()
    orders['order_purchase_timestamp'] = pd.to_datetime(orders['order_purchase_timestamp'])

    order_totals = (
        order_items
        .groupby('order_id', as_index=False)
        .agg(total_price=('price', 'sum'), total_freight=('freight_value', 'sum'))
    )

    order_data = orders.merge(order_totals, on='order_id', how='left')
    reference_date = order_data['order_purchase_timestamp'].max() + pd.Timedelta(days=1)

    customer_rfm = (
        order_data
        .groupby('customer_id', as_index=False)
        .agg(
            recency_days=('order_purchase_timestamp', lambda x: (reference_date - x.max()).days),
            frequency=('order_id', 'nunique'),
            monetary=('total_price', 'sum'),
        )
    )
    customer_rfm['monetary'] = customer_rfm['monetary'].fillna(0.0)

    # Map to customer_unique_id
    customer_rfm = customer_rfm.merge(
        customers[['customer_id', 'customer_unique_id']], on='customer_id', how='left'
    )
    customer_rfm = customer_rfm.dropna(subset=['customer_unique_id'])
    logger.info(f'RFM computed for {len(customer_rfm):,} unique customers.')

    # Standardise & cluster
    logger.info('Running K-Means (k=4)…')
    scaler = StandardScaler()
    features = scaler.fit_transform(customer_rfm[['recency_days', 'frequency', 'monetary']])

    kmeans = KMeans(n_clusters=4, random_state=42, n_init=10)
    customer_rfm['cluster'] = kmeans.fit_predict(features)

    # Label clusters by composite score (low recency = good, high F & M = good)
    cluster_summary = (
        customer_rfm
        .groupby('cluster')
        .agg(
            avg_recency=('recency_days', 'mean'),
            avg_frequency=('frequency', 'mean'),
            avg_monetary=('monetary', 'mean'),
        )
        .reset_index()
    )
    cluster_summary['score'] = (
        -cluster_summary['avg_recency'].rank(ascending=False)  # lower recency = better
        + cluster_summary['avg_frequency'].rank(ascending=True)
        + cluster_summary['avg_monetary'].rank(ascending=True)
    )
    cluster_summary = cluster_summary.sort_values('score', ascending=False)

    segment_labels = ['Champions', 'Loyal', 'At Risk', 'Hibernating']
    cluster_order = dict(zip(cluster_summary['cluster'].tolist(), segment_labels))
    customer_rfm['segment_label'] = customer_rfm['cluster'].map(cluster_order)

    logger.info('Segment distribution:')
    for label, count in customer_rfm['segment_label'].value_counts().items():
        logger.info(f'  {label}: {count:,}')

    return customer_rfm[
        ['customer_unique_id', 'segment_label', 'recency_days', 'frequency', 'monetary']
    ].rename(columns={'recency_days': 'recency'})


def save_customer_segments(engine, customer_segments: pd.DataFrame) -> None:
    logger.info(f'Writing {len(customer_segments):,} customer segments to database…')
    customer_segments = customer_segments.copy()
    customer_segments['updated_at'] = datetime.utcnow()
    with engine.begin() as conn:
        conn.execute(text('TRUNCATE TABLE customer_segments'))
        customer_segments.to_sql('customer_segments', conn, if_exists='append', index=False)
    logger.info('customer_segments table updated.')


# ---------------------------------------------------------------------------
# Apriori — Market Basket Analysis
# ---------------------------------------------------------------------------
def build_product_recommendations(order_items: pd.DataFrame) -> pd.DataFrame:
    logger.info('Building transaction basket matrix for Apriori…')

    transactions_series = order_items.groupby('order_id')['product_id'].apply(set)
    num_transactions = len(transactions_series)

    # Calculate item frequencies first to filter out rare items
    item_counts = order_items['product_id'].value_counts()
    
    # The most popular item in Olist only has ~527 sales. 
    # We must use a very low minimum support to find any rules.
    min_support = 0.0002  # 0.02% support threshold (~20 sales)
    min_count = int(min_support * num_transactions)
    
    frequent_items = set(item_counts[item_counts >= min_count].index)
    unique_products = sorted(list(frequent_items))
    
    logger.info(f'  Total {num_transactions:,} transactions.')
    logger.info(f'  Filtering to {len(unique_products):,} frequent items (min_count={min_count}) to save memory.')

    if not unique_products:
        logger.warning('No items meet the minimum support threshold.')
        return pd.DataFrame(columns=['product_id', 'recommended_product_id', 'confidence', 'support'])

    # One-hot encode using ONLY the frequent items
    transactions = transactions_series.tolist()
    te = pd.DataFrame(
        [{p: (p in tx) for p in unique_products} for tx in transactions],
        columns=unique_products,
        dtype=bool,
    )

    logger.info(f'Running FPGrowth (min_support={min_support})…')
    from mlxtend.frequent_patterns import fpgrowth
    frequent_itemsets = fpgrowth(te, min_support=min_support, use_colnames=True)

    if frequent_itemsets.empty:
        logger.warning('No frequent itemsets found — try lowering min_support.')
        return pd.DataFrame(columns=['product_id', 'recommended_product_id', 'confidence', 'support'])

    logger.info(f'  Found {len(frequent_itemsets):,} frequent itemsets.')

    rules = association_rules(frequent_itemsets, metric='confidence', min_threshold=0.1)
    if rules.empty:
        logger.warning('No association rules found.')
        return pd.DataFrame(columns=['product_id', 'recommended_product_id', 'confidence', 'support'])

    logger.info(f'  Generated {len(rules):,} association rules.')

    rows = []
    for _, row in rules.iterrows():
        for ant in sorted(row['antecedents']):
            for con in sorted(row['consequents']):
                if ant != con:
                    rows.append({
                        'product_id': ant,
                        'recommended_product_id': con,
                        'confidence': float(row['confidence']),
                        'support': float(row['support']),
                    })

    recommendations = (
        pd.DataFrame(rows)
        .sort_values(['product_id', 'confidence', 'support'], ascending=[True, False, False])
        .drop_duplicates(['product_id', 'recommended_product_id'])
        .groupby('product_id')
        .head(3)
        .reset_index(drop=True)
    )
    logger.info(f'  {len(recommendations):,} product recommendation pairs retained (top-3 per product).')
    return recommendations


def save_product_recommendations(engine, recommendations: pd.DataFrame) -> None:
    if recommendations.empty:
        logger.warning('Recommendations table is empty — skipping insert.')
        return
    logger.info(f'Writing {len(recommendations):,} product recommendations to database…')
    recommendations = recommendations.copy()
    recommendations['updated_at'] = datetime.utcnow()
    with engine.begin() as conn:
        conn.execute(text('TRUNCATE TABLE product_recommendations'))
        recommendations.to_sql('product_recommendations', conn, if_exists='append', index=False)
    logger.info('product_recommendations table updated.')


# ---------------------------------------------------------------------------
# Entry point
# ---------------------------------------------------------------------------
def run() -> None:
    logger.info('=' * 60)
    logger.info('  Olist Data Mining Engine — starting')
    logger.info(f'  DB: {_DB_HOST}:{_DB_PORT}/{_DB_NAME}')
    logger.info('=' * 60)

    engine = get_engine()

    orders, order_items, customers = load_data(engine)

    logger.info('-' * 60)
    logger.info('PHASE 1 — K-Means RFM Segmentation')
    logger.info('-' * 60)
    segments = build_rfm(orders, order_items, customers)
    save_customer_segments(engine, segments)

    logger.info('-' * 60)
    logger.info('PHASE 2 — Apriori Market Basket Analysis')
    logger.info('-' * 60)
    recs = build_product_recommendations(order_items)
    save_product_recommendations(engine, recs)

    logger.info('=' * 60)
    logger.info('  Data mining engine completed successfully.')
    logger.info('=' * 60)


if __name__ == '__main__':
    try:
        run()
    except Exception as exc:
        logger.exception('Fatal error in data mining engine: %s', exc)
        sys.exit(1)

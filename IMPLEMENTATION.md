# E-Commerce Intelligence Dashboard Implementation Guide

## Arsitektur
- Laravel 13 sebagai frontend dan manajemen database.
- Python 3 background engine untuk proses data mining berat.
- MySQL sebagai database relasional utama.

## Struktur yang Diterapkan
- `database/migrations/2026_06_04_000000_create_customers_table.php`
- `database/migrations/2026_06_04_000001_create_orders_table.php`
- `database/migrations/2026_06_04_000002_create_order_items_table.php`
- `database/migrations/2026_06_04_000003_create_products_table.php`
- `database/migrations/2026_06_04_000004_create_customer_segments_table.php`
- `database/migrations/2026_06_04_000005_create_product_recommendations_table.php`

- `app/Models/Customer.php`
- `app/Models/Order.php`
- `app/Models/OrderItem.php`
- `app/Models/Product.php`
- `app/Models/CustomerSegment.php`
- `app/Models/ProductRecommendation.php`

- `app/DataMining/data_mining_engine.py`
- `requirements.txt`
- `app/Console/Commands/RunDataMining.php`
- `app/Console/Kernel.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`
- `routes/web.php`
- `routes/console.php`

## Langkah Implementasi

1. Siapkan database MySQL dengan kredensial yang ada di `.env`.
2. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```
3. Install dependensi Python:
   ```bash
   python3 -m pip install -r requirements.txt
   ```
4. Jalankan engine data mining sekali untuk mengisi tabel hasil:
   ```bash
   php artisan data-mining:run
   ```
5. Buka dashboard di browser:
   ```bash
   php artisan serve
   ```
   lalu akses `http://127.0.0.1:8000`

## Scheduler
- Command Laravel: `data-mining:run`
- Penjadwalan otomatis di `app/Console/Kernel.php` pada `dailyAt('00:00')`

## Catatan
- Python script membaca kredensial MySQL dari file `.env`.
- RFM clustering disimpan di tabel `customer_segments`.
- Market basket analysis disimpan di tabel `product_recommendations`.


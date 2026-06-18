<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * OlistSeeder
 *
 * Imports all Olist CSV datasets directly from the /Dataset folder into MySQL.
 *
 * CSV files expected in: <project_root>/Dataset/
 *   - olist_customers_dataset.csv
 *   - olist_sellers_dataset.csv
 *   - olist_products_dataset.csv
 *   - olist_orders_dataset.csv
 *   - olist_order_items_dataset.csv
 *   - olist_order_payments_dataset.csv
 *   - olist_order_reviews_dataset.csv
 *   - product_category_name_translation.csv (skipped – no separate table)
 *
 * Usage:
 *   php artisan db:seed --class=OlistSeeder
 *   php artisan db:seed   (if registered in DatabaseSeeder)
 */
class OlistSeeder extends Seeder
{
    private const CHUNK = 500;

    /**
     * Map: table => [csv_file, nullable_columns, datetime_columns, column_renames]
     * Import order matters because of foreign-key constraints.
     */
    private const TABLES = [
        // 0. Geolocations (no FK dependencies)
        'geolocations' => [
            'file'      => 'olist_geolocation_dataset.csv',
            'nullable'  => [],
            'datetimes' => [],
            'renames'   => [],
            'skip_pk'   => true, // auto-increment id, no composite PK from CSV
        ],
        // 0.5. Product Category Translations (no FK dependencies)
        'product_category_name_translations' => [
            'file'      => 'product_category_name_translation.csv',
            'nullable'  => [],
            'datetimes' => [],
            'renames'   => [],
        ],
        // 1. Customers (no FK dependencies)
        'customers' => [
            'file'      => 'olist_customers_dataset.csv',
            'nullable'  => ['customer_zip_code_prefix', 'customer_city', 'customer_state'],
            'datetimes' => [],
            'renames'   => [],
        ],
        // 2. Sellers (no FK dependencies)
        'sellers' => [
            'file'      => 'olist_sellers_dataset.csv',
            'nullable'  => ['seller_zip_code_prefix', 'seller_city', 'seller_state'],
            'datetimes' => [],
            'renames'   => [],
        ],
        // 3. Products (no FK dependencies)
        'products' => [
            'file'      => 'olist_products_dataset.csv',
            'nullable'  => [
                'product_category_name',
                'product_name_length',
                'product_description_length',
                'product_photos_qty',
                'product_weight_g',
                'product_length_cm',
                'product_height_cm',
                'product_width_cm',
            ],
            'datetimes' => [],
            // Fix typos in original CSV column names
            'renames'   => [
                'product_name_lenght'        => 'product_name_length',
                'product_description_lenght' => 'product_description_length',
            ],
        ],
        // 4. Orders (FK → customers)
        'orders' => [
            'file'      => 'olist_orders_dataset.csv',
            'nullable'  => [
                'order_approved_at',
                'order_delivered_carrier_date',
                'order_delivered_customer_date',
                'order_estimated_delivery_date',
            ],
            'datetimes' => [
                'order_purchase_timestamp',
                'order_approved_at',
                'order_delivered_carrier_date',
                'order_delivered_customer_date',
                'order_estimated_delivery_date',
            ],
            'renames'   => [],
        ],
        // 5. Order Items (FK → orders)
        'order_items' => [
            'file'      => 'olist_order_items_dataset.csv',
            'nullable'  => [],
            'datetimes' => ['shipping_limit_date'],
            'renames'   => [],
        ],
        // 6. Order Payments (FK → orders)
        'order_payments' => [
            'file'      => 'olist_order_payments_dataset.csv',
            'nullable'  => ['payment_type'],
            'datetimes' => [],
            'renames'   => [],
            'skip_pk'   => true, // auto-increment id, no composite PK from CSV
        ],
        // 7. Order Reviews (FK → orders)
        'order_reviews' => [
            'file'      => 'olist_order_reviews_dataset.csv',
            'nullable'  => [
                'review_score',
                'review_comment_title',
                'review_comment_message',
                'review_creation_date',
                'review_answer_timestamp',
            ],
            'datetimes' => ['review_creation_date', 'review_answer_timestamp'],
            'renames'   => [],
        ],
    ];

    public function run(): void
    {
        // Dataset folder is at project root /Dataset
        $dataDir = base_path('Dataset');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach (self::TABLES as $table => $config) {
            $path = $dataDir . DIRECTORY_SEPARATOR . $config['file'];

            if (! file_exists($path)) {
                $this->command->warn("⚠️  Skipping [{$table}] — file not found: {$path}");
                continue;
            }

            $this->command->info("📦 Importing [{$table}] from {$config['file']}…");
            $this->importCsv(
                $table,
                $path,
                $config['nullable'],
                $config['datetimes'],
                $config['renames'],
            );
            $this->command->info("   ✅ [{$table}] done.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->newLine();
        $this->command->info('🎉 Olist full dataset import complete!');
    }

    private function importCsv(
        string $table,
        string $path,
        array $nullableCols,
        array $datetimeCols,
        array $renames = [],
    ): void {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command->error("Cannot open file: {$path}");
            return;
        }

        // Read & normalise header row
        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            $this->command->warn("   Empty file: {$path}");
            return;
        }

        // Remove BOM from the first header if present (UTF-8 BOM is \xEF\xBB\xBF)
        $headers[0] = preg_replace('/^[\xef\xbb\xbf]+/', '', $headers[0]);
        $headers = array_map('trim', $headers);

        // Apply column renames on headers
        $headers = array_map(fn($h) => $renames[$h] ?? $h, $headers);

        // Truncate destination table before import
        DB::table($table)->truncate();

        $batch = [];
        $total = 0;
        $now   = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue; // Skip malformed rows
            }

            $record = array_combine($headers, $row);

            // Sanitise nullable columns (empty string → null)
            foreach ($nullableCols as $col) {
                if (isset($record[$col]) && trim($record[$col]) === '') {
                    $record[$col] = null;
                }
            }

            // Sanitise datetime columns (empty string / 'NaT' / '0000-00-00 00:00:00' → null)
            foreach ($datetimeCols as $col) {
                if (isset($record[$col])) {
                    $val = trim($record[$col]);
                    if ($val === '' || $val === 'NaT' || $val === '0000-00-00 00:00:00') {
                        $record[$col] = null;
                    }
                }
            }

            $record['created_at'] = $now;
            $record['updated_at'] = $now;

            $batch[] = $record;
            $total++;

            if (count($batch) >= self::CHUNK) {
                DB::table($table)->insert($batch);
                $batch = [];
                // Progress feedback every 10k rows
                if ($total % 10000 === 0) {
                    $this->command->line("   → {$total} rows so far…");
                }
            }
        }

        if (! empty($batch)) {
            DB::table($table)->insert($batch);
        }

        fclose($handle);
        $this->command->line("   → {$total} rows inserted.");
    }
}

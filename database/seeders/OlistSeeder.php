<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * OlistSeeder
 *
 * Imports the four core Olist CSV files into MySQL.
 *
 * Expected file layout (place CSVs here before running):
 *   database/seeders/data/olist_customers_dataset.csv
 *   database/seeders/data/olist_orders_dataset.csv
 *   database/seeders/data/olist_order_items_dataset.csv
 *   database/seeders/data/olist_products_dataset.csv
 *
 * Usage:
 *   php artisan db:seed --class=OlistSeeder
 *   php artisan db:seed   (if registered in DatabaseSeeder)
 */
class OlistSeeder extends Seeder
{
    private const CHUNK = 500;

    /** Map: table => [csv_file, nullable_columns, datetime_columns] */
    private const TABLES = [
        'customers' => [
            'file'      => 'olist_customers_dataset.csv',
            'nullable'  => ['customer_zip_code_prefix', 'customer_city', 'customer_state'],
            'datetimes' => [],
        ],
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
        ],
        'order_items' => [
            'file'      => 'olist_order_items_dataset.csv',
            'nullable'  => [],
            'datetimes' => ['shipping_limit_date'],
        ],
        'products' => [
            'file'      => 'olist_products_dataset.csv',
            'nullable'  => [
                'product_category_name',
                'product_name_lenght',
                'product_description_lenght',
                'product_photos_qty',
                'product_weight_g',
                'product_length_cm',
                'product_height_cm',
                'product_width_cm',
            ],
            'datetimes' => [],
        ],
    ];

    public function run(): void
    {
        $dataDir = database_path('seeders/data');

        foreach (self::TABLES as $table => $config) {
            $path = $dataDir . DIRECTORY_SEPARATOR . $config['file'];

            if (! file_exists($path)) {
                $this->command->warn("⚠️  Skipping [{$table}] — file not found: {$path}");
                continue;
            }

            $this->command->info("📦 Importing [{$table}] from {$config['file']}…");
            $this->importCsv($table, $path, $config['nullable'], $config['datetimes']);
            $this->command->info("   ✅ [{$table}] done.");
        }

        $this->command->newLine();
        $this->command->info('🎉 Olist dataset import complete!');
    }

    private function importCsv(
        string $table,
        string $path,
        array $nullableCols,
        array $datetimeCols,
    ): void {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command->error("Cannot open file: {$path}");
            return;
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);
            $this->command->warn("   Empty file: {$path}");
            return;
        }
        $headers = array_map('trim', $headers);

        // Truncate destination table before import
        DB::table($table)->truncate();

        $batch  = [];
        $total  = 0;
        $now    = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue; // Skip malformed rows
            }

            $record = array_combine($headers, $row);

            // Sanitise nullable columns (empty string → null)
            foreach ($nullableCols as $col) {
                if (isset($record[$col]) && $record[$col] === '') {
                    $record[$col] = null;
                }
            }

            // Sanitise datetime columns (empty string / 'NaT' → null)
            foreach ($datetimeCols as $col) {
                if (isset($record[$col]) && (trim($record[$col]) === '' || $record[$col] === 'NaT')) {
                    $record[$col] = null;
                }
            }

            $record['created_at'] = $now;
            $record['updated_at'] = $now;

            $batch[] = $record;
            $total++;

            if (count($batch) >= self::CHUNK) {
                DB::table($table)->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table($table)->insert($batch);
        }

        fclose($handle);
        $this->command->line("   → {$total} rows inserted.");
    }
}

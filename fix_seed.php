<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::table('product_category_name_translations')->truncate();

$file = fopen(base_path('Dataset/product_category_name_translation.csv'), 'r');
$header = fgetcsv($file);
$header[0] = preg_replace('/^[\xef\xbb\xbf]+/', '', $header[0]);
$batch = [];

while (($row = fgetcsv($file)) !== false) {
    $data = array_combine($header, $row);
    $data['created_at'] = now();
    $data['updated_at'] = now();
    $batch[] = $data;
}

Illuminate\Support\Facades\DB::table('product_category_name_translations')->insert($batch);
echo 'Translations seeded!';

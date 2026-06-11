<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run with: php artisan db:seed
     * Or a specific seeder: php artisan db:seed --class=OlistSeeder
     */
    public function run(): void
    {
        $this->call([
            OlistSeeder::class,
        ]);
    }
}

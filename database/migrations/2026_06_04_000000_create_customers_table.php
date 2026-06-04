<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('customer_id', 64)->primary();
            $table->string('customer_unique_id', 64)->index();
            $table->string('customer_zip_code_prefix', 20)->nullable();
            $table->string('customer_city', 100)->nullable();
            $table->string('customer_state', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 64)->primary();
            $table->string('product_category_name', 150)->nullable();
            $table->unsignedSmallInteger('product_name_length')->nullable();
            $table->unsignedSmallInteger('product_description_length')->nullable();
            $table->unsignedSmallInteger('product_photos_qty')->nullable();
            $table->unsignedInteger('product_weight_g')->nullable();
            $table->unsignedInteger('product_length_cm')->nullable();
            $table->unsignedInteger('product_height_cm')->nullable();
            $table->unsignedInteger('product_width_cm')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

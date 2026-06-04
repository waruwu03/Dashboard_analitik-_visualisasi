<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 64)->index();
            $table->string('recommended_product_id', 64)->index();
            $table->decimal('confidence', 6, 4);
            $table->decimal('support', 6, 4);
            $table->timestamps();

            $table->unique(['product_id', 'recommended_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recommendations');
    }
};

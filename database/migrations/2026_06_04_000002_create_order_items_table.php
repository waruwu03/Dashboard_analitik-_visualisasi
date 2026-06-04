<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->string('order_id', 64);
            $table->unsignedSmallInteger('order_item_id');
            $table->string('product_id', 64)->index();
            $table->string('seller_id', 64)->nullable();
            $table->timestamp('shipping_limit_date')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('freight_value', 12, 2)->default(0);
            $table->timestamps();

            $table->primary(['order_id', 'order_item_id']);
            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

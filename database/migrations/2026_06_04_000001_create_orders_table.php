<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id', 64)->primary();
            $table->string('customer_id', 64)->index();
            $table->string('order_status', 50)->nullable();
            $table->timestamp('order_purchase_timestamp')->nullable();
            $table->timestamp('order_approved_at')->nullable();
            $table->timestamp('order_delivered_carrier_date')->nullable();
            $table->timestamp('order_delivered_customer_date')->nullable();
            $table->timestamp('order_estimated_delivery_date')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

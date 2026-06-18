<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 64)->index();
            $table->unsignedTinyInteger('payment_sequential')->default(1);
            $table->string('payment_type', 50)->nullable();
            $table->unsignedTinyInteger('payment_installments')->default(1);
            $table->decimal('payment_value', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};

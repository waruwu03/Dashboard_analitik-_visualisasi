<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->string('review_id', 64);
            $table->string('order_id', 64)->index();
            $table->unsignedTinyInteger('review_score')->nullable();
            $table->text('review_comment_title')->nullable();
            $table->text('review_comment_message')->nullable();
            $table->timestamp('review_creation_date')->nullable();
            $table->timestamp('review_answer_timestamp')->nullable();
            $table->timestamps();

            $table->primary(['review_id', 'order_id']);
            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_reviews');
    }
};

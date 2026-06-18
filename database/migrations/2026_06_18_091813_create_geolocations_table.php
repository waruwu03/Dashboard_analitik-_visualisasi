<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('geolocations', function (Blueprint $table) {
            $table->id();
            $table->string('geolocation_zip_code_prefix', 10)->index();
            $table->decimal('geolocation_lat', 10, 8);
            $table->decimal('geolocation_lng', 11, 8);
            $table->string('geolocation_city');
            $table->string('geolocation_state', 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geolocations');
    }
};

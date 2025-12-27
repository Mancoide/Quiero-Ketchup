<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('US');
            $table->json('coordinates')->nullable(); // {lat, lng} or use geography(POINT)
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // For PostGIS:
            // $table->geography('coordinates', 'point', 4326)->nullable();
            // DB::statement('CREATE INDEX locations_coordinates_gist_idx ON locations USING GIST (coordinates)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

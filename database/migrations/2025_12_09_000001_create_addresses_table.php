<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('street');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('PY');
            $table->json('location')->nullable(); // {lat, lng} or use geography(POINT) with PostGIS
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // For PostGIS support, uncomment and adjust:
            // $table->geography('location', 'point', 4326)->nullable();
            // Then create GIST index: DB::statement('CREATE INDEX addresses_location_gist_idx ON addresses USING GIST (location)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

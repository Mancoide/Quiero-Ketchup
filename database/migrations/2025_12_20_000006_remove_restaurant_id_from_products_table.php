<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'restaurant_id')) {
            return;
        }

        // PostgreSQL: hacer drops idempotentes para evitar errores si el índice/constraint no existe.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS products_restaurant_id_available_index');
            DB::statement('DROP INDEX IF EXISTS products_restaurant_id_index');
            DB::statement('ALTER TABLE products DROP CONSTRAINT IF EXISTS products_restaurant_id_foreign');
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index(['restaurant_id', 'available']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('promotions')) {
            return;
        }

        if (! Schema::hasColumn('promotions', 'restaurant_id')) {
            return;
        }

        // Preserve existing data by migrating legacy restaurant_id to the pivot table.
        if (Schema::hasTable('promotion_restaurant')) {
            $now = now();

            DB::table('promotions')
                ->whereNotNull('restaurant_id')
                ->select(['id', 'restaurant_id'])
                ->orderBy('id')
                ->chunkById(1000, function ($promotions) use ($now) {
                    $rows = [];

                    foreach ($promotions as $promotion) {
                        $rows[] = [
                            'promotion_id' => $promotion->id,
                            'restaurant_id' => $promotion->restaurant_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if ($rows === []) {
                        return;
                    }

                    DB::table('promotion_restaurant')->upsert(
                        $rows,
                        ['promotion_id', 'restaurant_id'],
                        ['updated_at']
                    );
                });
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Make the migration idempotent for PostgreSQL.
            DB::statement('ALTER TABLE promotions DROP CONSTRAINT IF EXISTS promotions_restaurant_id_foreign');
            DB::statement('DROP INDEX IF EXISTS promotions_restaurant_id_index');

            Schema::table('promotions', function (Blueprint $table) {
                $table->dropColumn('restaurant_id');
            });

            return;
        }

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurant_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('promotions')) {
            return;
        }

        if (Schema::hasColumn('promotions', 'restaurant_id')) {
            return;
        }

        Schema::table('promotions', function (Blueprint $table) {
            $table->foreignId('restaurant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }
};

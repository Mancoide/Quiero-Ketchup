<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table
                ->foreignId('subcategory_id')
                ->nullable()
                ->after('category_id')
                ->constrained('subcategories')
                ->nullOnDelete();

            $table->index(['category_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropIndex(['category_id', 'subcategory_id']);
            $table->dropColumn('subcategory_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            if (! Schema::hasColumn('reconciliations', 'total_reconciled_bank')) {
                $table->decimal('total_reconciled_bank', 18, 2)->nullable();
            }

            if (! Schema::hasColumn('reconciliations', 'total_reconciled_company')) {
                $table->decimal('total_reconciled_company', 18, 2)->nullable();
            }

            if (! Schema::hasColumn('reconciliations', 'total_unreconciled_bank')) {
                $table->decimal('total_unreconciled_bank', 18, 2)->nullable();
            }

            if (! Schema::hasColumn('reconciliations', 'total_unreconciled_company')) {
                $table->decimal('total_unreconciled_company', 18, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->dropColumn([
                'total_reconciled_bank',
                'total_reconciled_company',
                'total_unreconciled_bank',
                'total_unreconciled_company',
            ]);
        });
    }
};

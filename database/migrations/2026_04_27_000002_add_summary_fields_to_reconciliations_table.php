<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->json('summary_payload')->nullable()->after('possible_matches');
            $table->decimal('ledger_balance', 18, 2)->nullable()->after('summary_payload');
            $table->decimal('outstanding_checks', 18, 2)->nullable()->after('ledger_balance');
            $table->decimal('bank_unregistered_credits', 18, 2)->nullable()->after('outstanding_checks');
            $table->decimal('unbooked_debits', 18, 2)->nullable()->after('bank_unregistered_credits');
            $table->decimal('unbooked_credits', 18, 2)->nullable()->after('unbooked_debits');
            $table->decimal('reconciled_balance', 18, 2)->nullable()->after('unbooked_credits');
            $table->decimal('bank_statement_balance', 18, 2)->nullable()->after('reconciled_balance');
            $table->decimal('difference_amount', 18, 2)->nullable()->after('bank_statement_balance');
        });
    }

    public function down(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->dropColumn([
                'summary_payload',
                'ledger_balance',
                'outstanding_checks',
                'bank_unregistered_credits',
                'unbooked_debits',
                'unbooked_credits',
                'reconciled_balance',
                'bank_statement_balance',
                'difference_amount',
            ]);
        });
    }
};

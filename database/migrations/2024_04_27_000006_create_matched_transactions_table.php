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
        Schema::create('matched_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('system_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('reconciliation_result_id')->constrained()->onDelete('cascade');
            $table->decimal('match_score', 5, 2)->default(0);
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matched_transactions');
    }
};

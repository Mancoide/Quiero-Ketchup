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
        Schema::create('discrepancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('system_transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['only_in_bank', 'only_in_system', 'duplicate', 'amount_mismatch'])->default('only_in_bank');
            $table->decimal('amount_difference', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'resolved', 'ignored'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discrepancies');
    }
};

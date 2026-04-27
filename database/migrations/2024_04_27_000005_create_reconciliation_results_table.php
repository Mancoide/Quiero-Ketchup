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
        Schema::create('reconciliation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->onDelete('cascade');
            $table->foreignId('reconciliation_file_id')->constrained()->onDelete('cascade');
            $table->integer('total_matched')->default(0);
            $table->integer('total_unmatched_bank')->default(0);
            $table->integer('total_unmatched_system')->default(0);
            $table->decimal('discrepancy_amount', 15, 2)->default(0);
            $table->decimal('match_percentage', 5, 2)->default(0);
            $table->enum('status', ['pending_review', 'completed'])->default('pending_review');
            $table->integer('processing_time')->default(0); // en segundos
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_results');
    }
};

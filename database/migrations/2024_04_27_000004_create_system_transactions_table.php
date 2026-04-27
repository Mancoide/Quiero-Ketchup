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
        Schema::create('system_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_file_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('reference_number')->nullable();
            $table->enum('type', ['debit', 'credit'])->default('debit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_transactions');
    }
};

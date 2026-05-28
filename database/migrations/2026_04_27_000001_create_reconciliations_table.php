<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('bank_file_path');
            $table->string('bank_file_name');
            $table->string('company_file_path');
            $table->string('company_file_name');
            $table->string('result_file')->nullable();
            $table->unsignedInteger('total_bank_records')->default(0);
            $table->unsignedInteger('total_company_records')->default(0);
            $table->unsignedInteger('matched_records')->default(0);
            $table->unsignedInteger('bank_only_records')->default(0);
            $table->unsignedInteger('company_only_records')->default(0);
            $table->unsignedInteger('possible_matches')->default(0);
            $table->string('status')->default('pending');
            $table->longText('processing_log')->nullable();
            $table->longText('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};

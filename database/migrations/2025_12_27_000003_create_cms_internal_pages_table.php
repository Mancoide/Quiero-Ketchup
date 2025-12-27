<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_internal_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->constrained('cms_sections')
                ->cascadeOnDelete();

            $table->string('title');
            $table->longText('description');
            $table->string('status')->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['section_id', 'status']);
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_internal_pages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_banners', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->constrained('cms_sections')
                ->cascadeOnDelete();

            $table->string('button_text')->nullable();
            $table->string('image_link')->nullable();
            $table->string('button_link')->nullable();

            $table->string('status')->default('active');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['section_id', 'status']);
            $table->index(['section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_banners');
    }
};

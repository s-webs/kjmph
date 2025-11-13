<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_kk')->nullable();
            $table->longText('text_en')->nullable();
            $table->longText('text_ru')->nullable();
            $table->longText('text_kk')->nullable();
            $table->string('slug_en')->nullable();
            $table->string('slug_ru')->nullable();
            $table->string('slug_kk')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_kk')->nullable();
            $table->string('seo_keywords_en')->nullable();
            $table->string('seo_keywords_ru')->nullable();
            $table->string('seo_keywords_kk')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_kk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

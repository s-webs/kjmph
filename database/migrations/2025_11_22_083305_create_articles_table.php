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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->integer('author_id');
            $table->string('section');
            $table->string('material_lang');
            $table->string('cover')->nullable();
            $table->string('doi')->nullable();

            $table->string('title_en')->nullable();
            $table->string('title_ru')->nullable();
            $table->string('title_kk')->nullable();

            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_ru')->nullable();
            $table->string('subtitle_kk')->nullable();

            $table->longText('annotation_en')->nullable();
            $table->longText('annotation_ru')->nullable();
            $table->longText('annotation_kk')->nullable();

            $table->longText('literature_en')->nullable();
            $table->longText('literature_ru')->nullable();
            $table->longText('literature_kk')->nullable();

            $table->json('coauthors')->nullable();
            $table->json('metadata')->nullable();
            $table->json('publishing_process')->nullable();
            $table->json('history')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();

            $table->string('title_en')->nullable();
            $table->string('title_ru')->nullable();
            $table->string('title_kk')->nullable();

            $table->text('description_en')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kk')->nullable();

            $table->string('cover')->nullable();
            $table->string('file')->nullable();

            $table->string('url_id')->nullable();
            $table->string('doi')->nullable();

            $table->dateTime('published_at')->nullable();

            $table->string('slug_en')->nullable();
            $table->string('slug_ru')->nullable();
            $table->string('slug_kk')->nullable();

            $table->integer('volume')->nullable();
            $table->integer('number')->nullable();
            $table->integer('year')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};

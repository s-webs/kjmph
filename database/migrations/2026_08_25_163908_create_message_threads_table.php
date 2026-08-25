<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('type'); // editor_author, editor_reviewer
            $table->foreignId('reviewer_id')->nullable()->constrained('reviewers')->nullOnDelete();
            $table->timestamps();

            $table->unique(['article_id', 'type', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};

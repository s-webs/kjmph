<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('reviewers')->cascadeOnDelete();
            $table->string('status')->default('invited'); // invited, accepted, declined, completed
            $table->date('deadline')->nullable();
            $table->string('anonymized_title')->nullable();
            $table->string('anonymized_file')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_assignments');
    }
};

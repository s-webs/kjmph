<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_assignment_id')->constrained('review_assignments')->cascadeOnDelete();
            $table->string('decision'); // accept, minor_revision, major_revision, reject
            $table->text('author_comments')->nullable();
            $table->text('editor_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique('review_assignment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

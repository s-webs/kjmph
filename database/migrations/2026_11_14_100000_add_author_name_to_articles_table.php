<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Автор может быть задан вручную (без учётной записи).
            $table->integer('author_id')->nullable()->change();
            $table->string('author_name')->nullable()->after('author_id');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('author_name');
            $table->integer('author_id')->nullable(false)->change();
        });
    }
};

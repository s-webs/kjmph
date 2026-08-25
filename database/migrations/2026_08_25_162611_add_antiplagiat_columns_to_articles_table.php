<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('antiplagiat_doc_id')->nullable()->after('file');
            $table->float('antiplagiat_percent')->nullable()->after('antiplagiat_doc_id');
            $table->timestamp('antiplagiat_checked_at')->nullable()->after('antiplagiat_percent');
            $table->json('antiplagiat_report')->nullable()->after('antiplagiat_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'antiplagiat_doc_id',
                'antiplagiat_percent',
                'antiplagiat_checked_at',
                'antiplagiat_report',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('file')->nullable()->after('cover');
        });

        // Backfill from history JSON (last author_file_* value)
        $articles = DB::table('articles')->whereNotNull('history')->get(['id', 'history']);

        foreach ($articles as $article) {
            $history = json_decode($article->history, true);

            if (! is_array($history) || $history === []) {
                continue;
            }

            $filePath = null;

            foreach ($history as $key => $path) {
                if (is_string($key) && str_starts_with($key, 'author_file_') && is_string($path)) {
                    $filePath = $path;
                }
            }

            if ($filePath !== null) {
                DB::table('articles')->where('id', $article->id)->update(['file' => $filePath]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }
};

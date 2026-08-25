<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Release;

class JournalsController extends Controller
{
    public function release()
    {
        $release = Release::query()
            ->with([
                'articles' => static fn ($query) => $query
                    ->whereCurrentStatus('published')
                    ->with(['author', 'sectionModel']),
            ])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('year')
            ->orderByRaw('COALESCE(volume, 0) DESC')
            ->orderByDesc('number')
            ->orderByDesc('published_at')
            ->firstOrFail();

        $articlesBySection = $release->articles
            ->groupBy(static fn ($article) => $article->section_id ?: 0);

        return view('pages.journal.release', compact('release', 'articlesBySection'));
    }

    public function archive()
    {
        $items = Release::query()
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get();

        return view('pages.journal.archive', compact('items'));
    }
}

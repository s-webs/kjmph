<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\Section;

class JournalsController extends Controller
{
    public function release()
    {
        $release = Release::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->firstOrFail();

        return $this->renderRelease($release);
    }

    public function show(Release $release)
    {
        abort_unless($release->published_at && $release->published_at->lte(now()), 404);

        return $this->renderRelease($release);
    }

    private function renderRelease(Release $release)
    {
        $release->load([
            'articles' => static fn ($query) => $query
                ->whereCurrentStatus('published')
                ->with(['author', 'sectionModel']),
        ]);

        $articlesBySection = $release->articles
            ->groupBy(static fn ($article) => $article->section_id ?: (int) $article->section);
        $sections = Section::query()
            ->whereIn('id', $articlesBySection->keys()->filter()->all())
            ->orderBy('id')
            ->get();

        return view('pages.journal.release', compact('release', 'articlesBySection', 'sections'));
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

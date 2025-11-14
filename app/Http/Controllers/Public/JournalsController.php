<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\Section;
use Illuminate\Http\Request;

class JournalsController extends Controller
{
    public function release()
    {
        $sections = Section::all();
        $release = Release::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now()) // не показываем выпуски из будущего
            ->orderByDesc('year')
            ->orderByRaw('COALESCE(volume, 0) DESC') // если том может быть null
            ->orderByDesc('number')
            ->orderByDesc('published_at')
            ->firstOrFail();

        return view('pages.journal.release', compact('release', 'sections'));
    }

    public function archive()
    {
        $items = Release::all();

        return view('pages.journal.archive', compact('items'));
    }
}

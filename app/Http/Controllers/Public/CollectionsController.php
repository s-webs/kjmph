<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;

class CollectionsController extends Controller
{
    public function index()
    {
        $items = Collection::query()
            ->orderByDesc('published_at')
            ->get();

        return view('pages.collections.index', compact('items'));
    }
}

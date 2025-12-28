<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    public function index()
    {
        $items = Collection::all();

        return view('pages.collections.index', compact('items'));
    }
}

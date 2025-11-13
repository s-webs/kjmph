<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $localized_slug = 'slug_' . app()->getLocale();
        $page = Page::query()->where($localized_slug, 'home')->firstOrFail();
        return view('pages.home.index', compact('page'));
    }
}

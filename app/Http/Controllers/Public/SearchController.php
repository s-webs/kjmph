<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Collection;
use App\Models\Release;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $q = mb_substr($q, 0, 200);

        $articles = null;
        $releases = collect();
        $collections = collect();
        $authors = collect();
        $unavailable = false;

        if ($q !== '') {
            try {
                $now = now()->timestamp;

                // Сразу фильтруем по дате публикации выпуска: будущие выпуски не показываем.
                $articles = Article::search($q, static function ($index, string $query, array $options) use ($now) {
                    $options['filter'] = ["release_published_at <= {$now}"];

                    return $index->search($query, $options);
                })
                    ->query(static fn ($builder) => $builder->with(['author', 'release']))
                    ->paginate(10)
                    ->withQueryString();

                $releases = Release::search($q, static function ($index, string $query, array $options) use ($now) {
                    $options['filter'] = ["published_at <= {$now}"];

                    return $index->search($query, $options);
                })->take(6)->get();

                $collections = Collection::search($q)->take(6)->get();

                $authors = User::search($q)
                    ->query(static fn ($builder) => $builder->with([
                        'publishedArticles' => static fn ($rel) => $rel->with('release')->latest('id'),
                    ]))
                    ->take(8)
                    ->get();
            } catch (Throwable $e) {
                // Поиск не должен ронять сайт, если Meilisearch недоступен.
                Log::error('Search failed: ' . $e->getMessage());
                $unavailable = true;
                $articles = null;
            }
        }

        return view('pages.search.index', compact('q', 'articles', 'releases', 'collections', 'authors', 'unavailable'));
    }
}

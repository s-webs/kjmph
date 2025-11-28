<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreArticleRequest;
use App\Models\Article;
use App\Models\Section;
use App\MoonShine\Pages\SubmissionArticlePage;
use App\MoonShine\Pages\SubmittedArticlesPage;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{

    public function index()
    {
        $articles = Article::query()->where('author_id', '=', auth()->user()->id)->get();
        return view('pages.author.articles.index', compact('articles'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('pages.author.articles.create', compact('sections'));
    }

    public function store(StoreArticleRequest $request)
    {
        $data = $request->validated();
        $authorId = $request->user()->id ?? auth()->id();
        $coverPath = $request->file('cover')
            ? $request->file('cover')->store('articles/covers', 'public')
            : null;

        $file = $request->file('file');
        $filePath = $file->store('articles/files', 'public');

        $now = now();
        $historyKey = 'author_file_' . $now->format('Ymd_His');

        $history = [
            $historyKey => $filePath,
        ];

        $publishingProcess = [
            [
                'status' => 'submitted',
                'label' => 'article_submitted_by_author',
                'at' => $now->toDateTimeString(),
            ],
        ];

        $article = Article::query()->create([
            'author_id' => $authorId,
            'section' => $data['section'],
            'material_lang' => $data['material_lang'],
            'cover' => $coverPath,
            'doi' => null,

            'title_en' => $data['title_en'] ?? null,
            'title_ru' => $data['title_ru'] ?? null,
            'title_kk' => $data['title_kk'] ?? null,

            'subtitle_en' => $data['subtitle_en'] ?? null,
            'subtitle_ru' => $data['subtitle_ru'] ?? null,
            'subtitle_kk' => $data['subtitle_kk'] ?? null,

            'annotation_en' => $data['annotation_en'] ?? null,
            'annotation_ru' => $data['annotation_ru'] ?? null,
            'annotation_kk' => $data['annotation_kk'] ?? null,

            'literature_en' => $data['literature_en'] ?? null,
            'literature_ru' => $data['literature_ru'] ?? null,
            'literature_kk' => $data['literature_kk'] ?? null,

            'coauthors' => $data['coauthors'] ?? null,
            'metadata' => null,
            'publishing_process' => $publishingProcess,
            'history' => $history,
        ]);

        return redirect()
            ->route('author.articles.index')
            ->with('success', 'article_has_been_successfully_submitted');
    }

    public function details(Article $article)
    {
        return view('pages.author.articles.details', compact('article'));
    }
}

<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StoreArticleRequest;
use App\Mail\AuthorRevisionReceivedMail;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ArticlesController extends Controller
{
    public function index()
    {
        $articles = Article::query()->where('author_id', Auth::id())->get();

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
        $authorId = Auth::id();

        if ($authorId === null) {
            abort(403);
        }
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

        Article::query()->create([
            'author_id' => $authorId,
            'section' => $data['section'],
            'material_lang' => $data['material_lang'],
            'cover' => $coverPath,
            'file' => $filePath,
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
        $this->authorizeArticle($article);

        $article->load(['author', 'comments']);

        $reviewsForAuthor = $article->reviewAssignments()
            ->where('status', 'completed')
            ->with('review')
            ->get()
            ->pluck('review')
            ->filter();

        $relayMessages = $article->messageThreads()
            ->where('type', 'editor_author')
            ->with('messages')
            ->get()
            ->flatMap(static fn ($thread) => $thread->messages);

        return view('pages.author.articles.details', compact('article', 'reviewsForAuthor', 'relayMessages'));
    }

    public function uploadRevision(Request $request, Article $article)
    {
        $this->authorizeArticle($article);

        if (! in_array($article->current_status, ['literary_editing', 'awaiting_author_revision'], true)) {
            return redirect()
                ->route('author.article.details', $article)
                ->with('error', 'Сейчас нельзя загрузить исправления');
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'min:5'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $filePath = $request->file('file')->store('articles/files', 'public');

        $history = $article->history ?? [];
        $history['author_revision_' . now()->format('Ymd_His')] = $filePath;

        ArticleComment::query()->create([
            'article_id' => $article->id,
            'sender_type' => 'author',
            'message' => $data['message'],
            'attachment' => $filePath,
        ]);

        $article->forceFill([
            'file' => $filePath,
            'history' => $history,
        ])->save();

        $article->addProcessStep('revision_submitted', 'revision_submitted');

        $editorEmail = config('mail.editor_address') ?: config('mail.from.address');
        if (filled($editorEmail)) {
            Mail::to($editorEmail)->send(
                new AuthorRevisionReceivedMail($article, $data['message'])
            );
        }

        return redirect()
            ->route('author.article.details', $article)
            ->with('success', 'Исправленная версия отправлена редактору');
    }

    private function authorizeArticle(Article $article): void
    {
        abort_unless(
            (int) $article->author_id === (int) Auth::id(),
            403
        );
    }
}

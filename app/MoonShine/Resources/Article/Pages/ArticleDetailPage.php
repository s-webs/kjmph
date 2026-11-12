<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\Models\MessageThread;
use App\MoonShine\Actions\ArticleEditorialActions;
use App\MoonShine\Actions\AssignReviewersAction;
use App\MoonShine\Actions\PublishArticleAction;
use App\MoonShine\Actions\RelayMessageAction;
use App\MoonShine\Actions\SendToAntiplagiatAction;
use App\MoonShine\Resources\Article\ArticleResource;
use Illuminate\Http\Request;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use Throwable;

/** @extends DetailPage<ArticleResource> */
class ArticleDetailPage extends DetailPage
{

    protected function fields(): iterable
    {
        return [];
    }

    protected function buttons(): ListOf
    {
        /** @var Article|null $article */
        $article      = $this->getResource()->getItem();
        $articleModel = $article instanceof Article ? $article : null;

        return parent::buttons()
            ->add(SendToAntiplagiatAction::button($articleModel))
            ->add(AssignReviewersAction::button($articleModel))
            ->add(ArticleEditorialActions::sendToLiteraryEditingButton($articleModel))
            ->add(ArticleEditorialActions::acceptWithoutRevisionsButton($articleModel))
            ->add(ArticleEditorialActions::rejectButton($articleModel))
            ->add(PublishArticleAction::button($articleModel))
            ->add(RelayMessageAction::writeToAuthorButton($articleModel))
            ->add(RelayMessageAction::writeToReviewerButton($articleModel));
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        /** @var Article|null $article */
        $article = $this->getResource()->getItem();

        if (! $article instanceof Article) {
            return parent::mainLayer();
        }

        return [
            Box::make([
                FlexibleRender::make(fn () => $this->articleHeader($article)),
                ...$this->getTopButtons(),
            ]),
            Tabs::make([
                Tab::make('Основное',    [FlexibleRender::make(fn () => $this->tabMain($article))]),
                Tab::make('Антиплагиат',[FlexibleRender::make(fn () => $this->tabAntiplagiat($article))]),
                Tab::make('Рецензенты', [FlexibleRender::make(fn () => $this->tabReviewers($article))]),
                Tab::make('Переписка',  [FlexibleRender::make(fn () => $this->tabMessages($article))]),
                Tab::make('Содержание', [FlexibleRender::make(fn () => $this->tabContent($article))]),
                Tab::make('Метаданные', [FlexibleRender::make(fn () => $this->tabMeta($article))]),
            ]),
        ];
    }

    // ─── Header ──────────────────────────────────────────────────────────────

    private function articleHeader(Article $article): string
    {
        $title   = e($article->title_ru ?? $article->title_en ?? $article->title_kk ?? 'Без названия');
        $author  = e($article->author?->name ?? '—');
        $section = e($article->section ?? '—');
        $lang    = match ($article->material_lang) {
            'ru'    => '🇷🇺 Русский',
            'en'    => '🇬🇧 English',
            'kk'    => '🇰🇿 Қазақша',
            default => e((string) $article->material_lang),
        };

        [$statusText, $statusStyle] = $this->statusBadge($article->current_status ?? '');

        $cover = $article->cover
            ? '<img src="' . e(asset($article->cover)) . '" alt="cover" style="width:72px;height:100px;object-fit:cover;border-radius:8px;border:1px solid rgba(100,116,139,.4);flex-shrink:0">'
            : '<div style="width:72px;height:100px;border-radius:8px;background:rgba(30,41,59,.8);border:1px solid rgba(100,116,139,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#475569;font-size:22px">📄</div>';

        $cText  = 'color:#e2e8f0';
        $cMuted = 'color:#94a3b8';
        $id     = $article->id;
        $postedDate = $article->created_at?->format('d.m.Y') ?? '';

        return <<<HTML
<div style="display:flex;gap:16px;align-items:flex-start;margin-bottom:4px">
    {$cover}
    <div style="flex:1;min-width:0">
        <h1 style="font-size:18px;font-weight:700;{$cText};margin:0 0 8px;line-height:1.3">{$title}</h1>
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:6px">
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;{$statusStyle}">{$statusText}</span>
            <span style="{$cMuted}">·</span>
            <span style="font-size:13px;{$cMuted}">👤 {$author}</span>
            <span style="{$cMuted}">·</span>
            <span style="font-size:13px;{$cMuted}">📂 {$section}</span>
            <span style="{$cMuted}">·</span>
            <span style="font-size:13px;{$cMuted}">{$lang}</span>
        </div>
        <div style="font-size:12px;color:#475569">ID #{$id} · подана {$postedDate}</div>
    </div>
</div>
HTML;
    }

    // ─── Tabs ─────────────────────────────────────────────────────────────────

    private function tabMain(Article $article): string
    {
        $doi     = e($article->doi ?? '—');
        $release = e($article->release?->title_ru ?? ($article->release_id ? '#' . $article->release_id : 'Не назначен'));
        $date    = $article->created_at?->format('d.m.Y H:i') ?? '—';

        $file = $article->file
            ? '<a href="' . e(asset($article->file)) . '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;background:rgba(59,130,246,.15);color:#93c5fd;font-size:13px;font-weight:500;text-decoration:none;border:1px solid rgba(59,130,246,.3)">📄 Скачать файл статьи</a>'
            : '<span style="color:#64748b;font-size:13px">Файл не прикреплён</span>';

        $cover = $article->cover
            ? '<a href="' . e(asset($article->cover)) . '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;background:rgba(100,116,139,.15);color:#94a3b8;font-size:13px;font-weight:500;text-decoration:none;border:1px solid rgba(100,116,139,.3)">🖼 Посмотреть обложку</a>'
            : '<span style="color:#64748b;font-size:13px">Обложка не загружена</span>';

        return $this->wrap([
            $this->card('Идентификаторы', [
                $this->row('DOI', $doi),
                $this->row('Выпуск', $release),
                $this->row('Дата подачи', $date),
            ]),
            $this->card('Файлы', [
                $this->row('Файл статьи', $file),
                $this->row('Обложка', $cover),
            ]),
        ]);
    }

    private function tabAntiplagiat(Article $article): string
    {
        $percent   = $article->antiplagiat_percent;
        $docId     = e($article->antiplagiat_doc_id ?? '—');
        $checked   = $article->antiplagiat_checked_at?->format('d.m.Y H:i') ?? '—';
        $reportUrl = data_get($article->antiplagiat_report, 'report_url');

        $report = $reportUrl
            ? '<a href="' . e($reportUrl) . '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;background:rgba(99,102,241,.15);color:#a5b4fc;font-size:13px;font-weight:500;text-decoration:none;border:1px solid rgba(99,102,241,.3)">📊 Открыть отчёт</a>'
            : '<span style="color:#64748b;font-size:13px">—</span>';

        if ($percent === null) {
            $badge = '<span style="color:#64748b;font-size:13px">Не проверено</span>';
        } elseif ($percent >= 70) {
            $badge = "<span style='padding:4px 14px;border-radius:20px;background:rgba(34,197,94,.15);color:#4ade80;font-size:15px;font-weight:700;border:1px solid rgba(34,197,94,.3)'>✅ {$percent}%</span>";
        } elseif ($percent >= 50) {
            $badge = "<span style='padding:4px 14px;border-radius:20px;background:rgba(234,179,8,.15);color:#fbbf24;font-size:15px;font-weight:700;border:1px solid rgba(234,179,8,.3)'>⚠️ {$percent}%</span>";
        } else {
            $badge = "<span style='padding:4px 14px;border-radius:20px;background:rgba(239,68,68,.15);color:#f87171;font-size:15px;font-weight:700;border:1px solid rgba(239,68,68,.3)'>❌ {$percent}%</span>";
        }

        return $this->wrap([
            $this->card('Результаты проверки', [
                $this->row('Оригинальность', $badge),
                $this->row('Дата проверки', $checked),
                $this->row('Doc ID', $docId),
                $this->row('Отчёт', $report),
            ]),
        ]);
    }

    private function tabReviewers(Article $article): string
    {
        $assignments = $article->reviewAssignments()->with(['reviewer', 'review'])->get();

        if ($assignments->isEmpty()) {
            return $this->wrap([$this->empty('Рецензенты ещё не назначены')]);
        }

        $statusStyles = [
            'invited'   => 'background:rgba(234,179,8,.15);color:#fbbf24;border:1px solid rgba(234,179,8,.3)',
            'accepted'  => 'background:rgba(59,130,246,.15);color:#93c5fd;border:1px solid rgba(59,130,246,.3)',
            'declined'  => 'background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3)',
            'completed' => 'background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.3)',
        ];
        $statusLabels = [
            'invited' => 'Приглашён', 'accepted' => 'Принял',
            'declined' => 'Отказался', 'completed' => 'Завершил',
        ];
        $decisionColors = [
            'accept'         => '#4ade80',
            'minor_revision' => '#fbbf24',
            'major_revision' => '#fb923c',
            'reject'         => '#f87171',
        ];
        $decisionLabels = [
            'accept' => 'Принять', 'minor_revision' => 'Малые правки',
            'major_revision' => 'Существенные правки', 'reject' => 'Отклонить',
        ];

        $html = '';
        foreach ($assignments as $a) {
            $name      = e($a->reviewer?->name ?? '—');
            $email     = e($a->reviewer?->email ?? '');
            $sStyle    = $statusStyles[$a->status] ?? 'background:rgba(100,116,139,.2);color:#94a3b8';
            $sLabel    = $statusLabels[$a->status] ?? e($a->status);
            $deadline  = $a->deadline instanceof \DateTimeInterface ? $a->deadline->format('d.m.Y') : '—';

            $reviewBody = '';
            if ($a->review) {
                $dColor  = $decisionColors[$a->review->decision] ?? '#94a3b8';
                $dLabel  = $decisionLabels[$a->review->decision] ?? e($a->review->decision);
                $aComm   = nl2br(e((string) $a->review->author_comments));
                $eComm   = $a->review->editor_comments ? nl2br(e($a->review->editor_comments)) : null;

                $reviewBody  = "<div style='border-top:1px solid var(--color-base-stroke)'>";
                $reviewBody .= "<div style='padding:10px 16px;border-bottom:1px solid var(--color-base-stroke)'>" . $this->row('Решение', "<span style='font-weight:600;color:{$dColor}'>{$dLabel}</span>") . '</div>';
                $reviewBody .= "<div style='padding:10px 16px" . ($eComm ? ";border-bottom:1px solid var(--color-base-stroke)" : '') . "'><div style='font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-base-text);opacity:.5;margin-bottom:6px'>Комментарий автору</div><div style='font-size:13px;color:var(--color-base-text);line-height:1.6'>{$aComm}</div></div>";
                if ($eComm) {
                    $reviewBody .= "<div style='padding:10px 16px'><div style='font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-base-text);opacity:.5;margin-bottom:6px'>Комментарий редактору</div><div style='font-size:13px;color:var(--color-base-text);line-height:1.6'>{$eComm}</div></div>";
                }
                $reviewBody .= '</div>';
            }

            $html .= <<<CARD
<div style="border-radius:var(--ms-box-radius,8px);border:1px solid var(--color-base-stroke);overflow:hidden;margin-bottom:10px">
    <div style="padding:10px 16px;background:var(--color-base-100);border-bottom:1px solid var(--color-base-stroke);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
        <div>
            <div style="font-size:14px;font-weight:600;color:var(--color-base-text)">{$name}</div>
            <div style="font-size:12px;color:var(--color-base-text);opacity:.5">{$email}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;{$sStyle}">{$sLabel}</span>
            <span style="font-size:12px;color:var(--color-base-text);opacity:.5">до {$deadline}</span>
        </div>
    </div>
    <div style="background:var(--color-base)">{$reviewBody}</div>
</div>
CARD;
        }

        return $this->wrap([$html]);
    }

    private function tabMessages(Article $article): string
    {
        $parts = [];

        $comments = $article->comments()->orderBy('created_at')->get();
        if ($comments->isEmpty()) {
            $bubbles = $this->empty('Переписки пока нет');
        } else {
            $bubbles = '';
            foreach ($comments as $comment) {
                $isEditor  = $comment->sender_type === 'editor';
                $who       = $isEditor ? 'Редактор' : 'Автор';
                $bg        = $isEditor ? 'background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25)' : 'background:rgba(30,41,59,.6);border:1px solid rgba(100,116,139,.25)';
                $nameColor = $isEditor ? 'color:#93c5fd' : 'color:#cbd5e1';
                $date      = $comment->created_at?->format('d.m.Y H:i') ?? '';
                $msg       = nl2br(e($comment->message));
                $fileLink  = $comment->attachment
                    ? '<div style="margin-top:6px"><a href="' . e(asset($comment->attachment)) . '" target="_blank" style="color:#93c5fd;font-size:12px">📎 Вложение</a></div>'
                    : '';
                $bubbles .= "<div style='border-radius:8px;{$bg};padding:10px 14px;margin-bottom:8px'><div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:6px'><span style='font-size:12px;font-weight:600;{$nameColor}'>{$who}</span><span style='font-size:11px;color:var(--color-base-text);opacity:.45'>{$date}</span></div><div style='font-size:13px;color:var(--color-base-text);line-height:1.6'>{$msg}</div>{$fileLink}</div>";
            }
        }
        $parts[] = $this->cardRaw('Комментарии редакции', $bubbles);

        $threads = MessageThread::query()
            ->where('article_id', $article->id)
            ->with(['messages', 'reviewer'])
            ->get();

        foreach ($threads as $thread) {
            $threadTitle = $thread->type === 'editor_reviewer'
                ? 'Email ↔ Рецензент: ' . e($thread->reviewer?->name ?? '')
                : 'Email ↔ Автор';

            $msgs = '';
            foreach ($thread->messages as $message) {
                $who  = match ($message->sender_type) {
                    'editor' => 'Редактор', 'author' => 'Автор', 'reviewer' => 'Рецензент', default => e($message->sender_type),
                };
                $date = $message->sent_at?->format('d.m.Y H:i') ?? '';
                $body = nl2br(e($message->body));
                $msgs .= "<div style='border-left:2px solid var(--color-info);padding-left:12px;margin-bottom:12px'><div style='font-size:12px;margin-bottom:4px'><span style='font-weight:600;color:var(--color-base-text)'>{$who}</span> <span style='color:var(--color-base-text);opacity:.45'>{$date}</span></div><div style='font-size:13px;color:var(--color-base-text);line-height:1.6'>{$body}</div></div>";
            }
            $parts[] = $this->cardRaw($threadTitle, $msgs ?: $this->empty('Сообщений нет'));
        }

        return $this->wrap($parts);
    }

    private function tabContent(Article $article): string
    {
        $langs    = ['ru' => '🇷🇺 RU', 'en' => '🇬🇧 EN', 'kk' => '🇰🇿 KK'];
        $sections = [];

        foreach ($langs as $code => $flag) {
            $title      = $article->{"title_{$code}"} ?? '';
            $subtitle   = $article->{"subtitle_{$code}"} ?? '';
            $annotation = $article->{"annotation_{$code}"} ?? '';
            $literature = $article->{"literature_{$code}"} ?? '';

            if (! $title && ! $annotation) {
                continue;
            }

            $body  = '';
            if ($title) {
                $body .= "<div style='font-size:16px;font-weight:600;color:#e2e8f0;line-height:1.3;margin-bottom:4px'>" . e($title) . '</div>';
            }
            if ($subtitle) {
                $body .= "<div style='font-size:13px;color:#94a3b8;margin-bottom:10px'>" . e($subtitle) . '</div>';
            }
            if ($annotation) {
                $body .= "<div style='margin-top:10px'><div style='color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px'>Аннотация</div><div style='font-size:13px;color:#e2e8f0;line-height:1.7'>" . nl2br(e($annotation)) . '</div></div>';
            }
            if ($literature) {
                $body .= "<div style='margin-top:12px'><div style='color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px'>Список литературы</div><div style='font-size:13px;color:#94a3b8;line-height:1.7'>" . nl2br(e($literature)) . '</div></div>';
            }

            $sections[] = $this->cardRaw($flag, $body);
        }

        return empty($sections)
            ? $this->wrap([$this->empty('Содержание не заполнено')])
            : $this->wrap($sections);
    }

    private function tabMeta(Article $article): string
    {
        $coauthors = collect($article->coauthors ?? []);
        $process   = collect($article->publishing_process ?? []);

        if ($coauthors->isEmpty()) {
            $coBody = $this->empty('Нет соавторов');
        } else {
            $coBody = '';
            foreach ($coauthors as $i => $c) {
                $border = $i > 0 ? "border-top:1px solid rgba(100,116,139,.2);" : '';
                $name   = e($c['fullname'] ?? '—');
                $org    = isset($c['organisation']) && $c['organisation'] ? e($c['organisation']) : '';
                $role   = isset($c['role']) && $c['role'] ? e($c['role']) : '';
                $orcid  = isset($c['orcid']) && $c['orcid']
                    ? ' <a href="' . e($c['orcid']) . '" target="_blank" style="color:#818cf8;font-size:12px;text-decoration:none">ORCID ↗</a>'
                    : '';
                $coBody .= "<div style='padding:10px 0;{$border}display:flex;justify-content:space-between;align-items:center'><div><span style='font-size:13px;font-weight:500;color:#e2e8f0'>{$name}</span>{$orcid}<div style='font-size:12px;color:#64748b;margin-top:2px'>{$org}</div></div><span style='font-size:11px;color:#94a3b8'>{$role}</span></div>";
            }
        }

        if ($process->isEmpty()) {
            $procBody = $this->empty('Пусто');
        } else {
            $procBody = '';
            foreach ($process as $i => $step) {
                $border = $i > 0 ? "border-top:1px solid rgba(100,116,139,.2);" : '';
                $label  = e($step['label'] ?? $step['status'] ?? '—');
                $at     = e($step['at'] ?? '');
                $procBody .= "<div style='padding:8px 0;{$border}display:flex;justify-content:space-between'><span style='font-size:13px;font-weight:500;color:#e2e8f0'>{$label}</span><span style='font-size:12px;color:#64748b'>{$at}</span></div>";
            }
        }

        return $this->wrap([
            $this->cardRaw('Соавторы', $coBody),
            $this->cardRaw('Таймлайн публикации', $procBody),
        ]);
    }

    // ─── Layout helpers ───────────────────────────────────────────────────────

    private function wrap(array $parts): string
    {
        return '<div style="padding:16px;display:grid;gap:12px">' . implode('', $parts) . '</div>';
    }

    /** Карточка с рядами field() */
    private function card(string $title, array $rows): string
    {
        $items = '';
        foreach ($rows as $i => $row) {
            $border = $i > 0 ? 'border-top:1px solid var(--color-base-stroke);' : '';
            $items .= "<div style='padding:10px 16px;{$border}'>{$row}</div>";
        }

        return <<<HTML
<div style="border-radius:var(--ms-box-radius,8px);border:1px solid var(--color-base-stroke);overflow:hidden">
    <div style="padding:8px 16px;background:var(--color-base-100);border-bottom:1px solid var(--color-base-stroke)">
        <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-base-text);opacity:.55">{$title}</span>
    </div>
    <div style="background:var(--color-base)">{$items}</div>
</div>
HTML;
    }

    /** Карточка с произвольным HTML-контентом */
    private function cardRaw(string $title, string $content): string
    {
        return <<<HTML
<div style="border-radius:var(--ms-box-radius,8px);border:1px solid var(--color-base-stroke);overflow:hidden">
    <div style="padding:8px 16px;background:var(--color-base-100);border-bottom:1px solid var(--color-base-stroke)">
        <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-base-text);opacity:.55">{$title}</span>
    </div>
    <div style="background:var(--color-base);padding:12px 16px">{$content}</div>
</div>
HTML;
    }

    private function row(string $label, string $value): string
    {
        return "<div style='display:flex;gap:16px;align-items:center'><div style='width:130px;flex-shrink:0;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--color-base-text);opacity:.5'>{$label}</div><div style='font-size:13px;color:var(--color-base-text)'>{$value}</div></div>";
    }

    private function empty(string $msg): string
    {
        return "<div style='border-radius:8px;border:1px dashed var(--color-base-stroke);padding:20px;text-align:center;font-size:13px;color:var(--color-base-text);opacity:.4;font-style:italic'>{$msg}</div>";
    }

    private function statusBadge(string $status): array
    {
        return match ($status) {
            'submitted'        => ['Подана',          'background:rgba(100,116,139,.2);color:#94a3b8'],
            'antiplagiat'      => ['Антиплагиат',     'background:rgba(168,85,247,.15);color:#c084fc'],
            'review'           => ['Рецензирование',  'background:rgba(59,130,246,.15);color:#93c5fd'],
            'literary_editing' => ['Лит. редактура',  'background:rgba(99,102,241,.15);color:#a5b4fc'],
            'accepted'         => ['Принята',         'background:rgba(34,197,94,.15);color:#4ade80'],
            'rejected'         => ['Отклонена',       'background:rgba(239,68,68,.15);color:#f87171'],
            'published'        => ['Опубликована',    'background:rgba(16,185,129,.15);color:#34d399'],
            default            => [e($status) ?: '—', 'background:rgba(100,116,139,.2);color:#94a3b8'],
        };
    }

    // ─── Async handlers ───────────────────────────────────────────────────────

    #[AsyncMethod]
    public function sendToAntiplagiat(): void
    {
        SendToAntiplagiatAction::handle($this->requireArticle());
    }

    #[AsyncMethod]
    public function assignReviewers(Request $request): void
    {
        AssignReviewersAction::handle(
            $this->requireArticle(),
            $request->input('reviewer_ids'),
            $request->input('deadline'),
        );
    }

    #[AsyncMethod]
    public function sendToLiteraryEditing(Request $request): void
    {
        ArticleEditorialActions::sendToLiteraryEditing(
            $this->requireArticle(),
            $request->input('message'),
        );
    }

    #[AsyncMethod]
    public function rejectArticle(Request $request): void
    {
        ArticleEditorialActions::reject(
            $this->requireArticle(),
            $request->input('message'),
        );
    }

    #[AsyncMethod]
    public function acceptWithoutRevisions(): void
    {
        ArticleEditorialActions::acceptWithoutRevisions($this->requireArticle());
    }

    #[AsyncMethod]
    public function publishArticle(Request $request): void
    {
        PublishArticleAction::handle(
            $this->requireArticle(),
            $request->input('release_id'),
            $request->input('section_id'),
            $request->input('doi'),
        );
    }

    #[AsyncMethod]
    public function relayMessageToAuthor(Request $request): void
    {
        RelayMessageAction::sendToAuthor(
            $this->requireArticle(),
            $request->input('body'),
        );
    }

    #[AsyncMethod]
    public function relayMessageToReviewer(Request $request): void
    {
        RelayMessageAction::sendToReviewer(
            $this->requireArticle(),
            $request->input('reviewer_id'),
            $request->input('body'),
        );
    }

    private function requireArticle(): Article
    {
        $article = $this->getResource()->getItem();

        if (! $article instanceof Article) {
            throw new \RuntimeException('Статья не найдена');
        }

        return $article;
    }
}

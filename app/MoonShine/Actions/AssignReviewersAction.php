<?php

declare(strict_types=1);

namespace App\MoonShine\Actions;

use App\Mail\ReviewerInvitationMail;
use App\Models\Article;
use App\Models\ReviewAssignment;
use App\Models\Reviewer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use RuntimeException;

final class AssignReviewersAction
{
    public static function button(?Article $article = null): ActionButtonContract
    {
        $availableReviewers = self::availableReviewers($article);

        $button = ActionButton::make('Назначить рецензентов')
            ->method(
                method: 'assignReviewers',
                message: 'Рецензенты назначены',
            )
            ->icon('user-plus')
            ->success()
            ->canSee(static function (mixed $item) use ($availableReviewers): bool {
                return $item instanceof Article
                    && filled($item->file)
                    && $availableReviewers !== [];
            })
            ->withConfirm(
                title: 'Назначить рецензентов',
                content: 'После назначения рецензенты получат email со ссылкой на портал.',
                button: 'Назначить',
                fields: static function () use ($availableReviewers): array {
                    return [
                        Select::make('Рецензенты', 'reviewer_ids')
                            ->options($availableReviewers)
                            ->multiple()
                            ->searchable()
                            ->required(),
                        Date::make('Дедлайн', 'deadline')
                            ->required(),
                    ];
                },
            );

        return $button;
    }

    /**
     * @param  list<int|string>|int|string|null  $reviewerIds
     */
    public static function handle(Article $article, mixed $reviewerIds, mixed $deadline): void
    {
        if (! filled($article->file)) {
            throw new RuntimeException('У статьи нет файла для рецензирования');
        }

        $ids = collect(is_array($reviewerIds) ? $reviewerIds : [$reviewerIds])
            ->filter(static fn ($id) => filled($id))
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            throw ValidationException::withMessages([
                'reviewer_ids' => 'Выберите хотя бы одного рецензента',
            ]);
        }

        if (! filled($deadline)) {
            throw ValidationException::withMessages([
                'deadline' => 'Укажите дедлайн',
            ]);
        }

        $alreadyAssigned = $article->reviewAssignments()->pluck('reviewer_id')->all();
        $ids = array_values(array_diff($ids, $alreadyAssigned));

        if ($ids === []) {
            throw ValidationException::withMessages([
                'reviewer_ids' => 'Выбранные рецензенты уже назначены',
            ]);
        }

        $reviewers = Reviewer::query()
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get();

        if ($reviewers->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'reviewer_ids' => 'Некоторые рецензенты недоступны',
            ]);
        }

        $anonymizedTitle = self::anonymizedTitle($article);
        $sourceDisk = 'public';
        $sourcePath = $article->file;
        $extension = pathinfo((string) $sourcePath, PATHINFO_EXTENSION) ?: 'bin';

        if (! Storage::disk($sourceDisk)->exists($sourcePath)) {
            throw new RuntimeException('Файл статьи не найден в хранилище');
        }

        $created = DB::transaction(function () use ($article, $reviewers, $deadline, $anonymizedTitle, $sourceDisk, $sourcePath, $extension) {
            $assignments = [];

            foreach ($reviewers as $reviewer) {
                $assignment = ReviewAssignment::query()->create([
                    'article_id' => $article->id,
                    'reviewer_id' => $reviewer->id,
                    'status' => 'invited',
                    'deadline' => $deadline,
                    'anonymized_title' => $anonymizedTitle,
                    'invited_at' => now(),
                    'token' => (string) Str::uuid(),
                ]);

                $destPath = "review_files/{$assignment->id}/manuscript.{$extension}";
                Storage::disk('local')->put(
                    $destPath,
                    Storage::disk($sourceDisk)->get($sourcePath)
                );

                $assignment->forceFill(['anonymized_file' => $destPath])->save();
                $assignments[] = $assignment->load('reviewer');
            }

            if ($article->current_status !== 'under_review') {
                $article->addProcessStep('under_review', 'under_review', [
                    'reviewers_count' => count($assignments),
                ]);
            }

            return $assignments;
        });

        foreach ($created as $assignment) {
            Mail::to($assignment->reviewer->email)->send(
                new ReviewerInvitationMail($assignment)
            );
        }

        toast('Рецензенты назначены, приглашения отправлены', ToastType::SUCCESS);
    }

    /**
     * @return array<int, string>
     */
    private static function availableReviewers(?Article $article): array
    {
        if (! $article instanceof Article) {
            return [];
        }

        $assignedIds = $article->reviewAssignments()->pluck('reviewer_id');

        return Reviewer::query()
            ->where('is_active', true)
            ->when($assignedIds->isNotEmpty(), static fn ($q) => $q->whereNotIn('id', $assignedIds))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(static fn (Reviewer $reviewer): array => [
                $reviewer->id => $reviewer->name . ' (' . $reviewer->email . ')',
            ])
            ->all();
    }

    private static function anonymizedTitle(Article $article): string
    {
        $title = (string) ($article->title ?? $article->title_ru ?? $article->title_en ?? $article->title_kk ?? '');

        return filled($title) ? $title : 'Manuscript #' . $article->id;
    }
}

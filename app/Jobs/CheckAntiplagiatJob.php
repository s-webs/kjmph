<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\Antiplagiat\AntiplagiatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CheckAntiplagiatJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(
        public int $articleId,
    ) {
    }

    public function handle(AntiplagiatService $antiplagiat): void
    {
        $article = Article::query()->with('author')->find($this->articleId);

        if ($article === null) {
            return;
        }

        if (! filled($article->file)) {
            $article->addProcessStep('antiplagiat_failed', 'antiplagiat_failed', [
                'error' => 'У статьи отсутствует файл для проверки',
            ]);

            return;
        }

        $filepath = Storage::disk('public')->path($article->file);

        if (! is_file($filepath) || ! is_readable($filepath)) {
            $article->addProcessStep('antiplagiat_failed', 'antiplagiat_failed', [
                'error' => "Файл не найден или недоступен: {$article->file}",
            ]);

            return;
        }

        $article->addProcessStep('antiplagiat_pending', 'antiplagiat_pending');

        try {
            $author = $article->author;
            $authorName = (string) ($author?->name ?? 'Author');
            $nameParts = preg_split('/\s+/u', trim($authorName), -1, PREG_SPLIT_NO_EMPTY) ?: ['Author'];

            $result = $antiplagiat->runFullCheckFromPath(
                filepath: $filepath,
                externalUserId: (string) ($author?->id ?? $article->id),
                authorData: [
                    'surname' => $nameParts[0] ?? 'Author',
                    'otherNames' => implode(' ', array_slice($nameParts, 1)) ?: $authorName,
                    'customId' => 'author_' . ($author?->id ?? $article->id),
                ],
            );

            $report = $this->normalizeReport($result);
            $percent = $this->extractOriginalityPercent($report);

            $article->refresh();
            $article->forceFill([
                'antiplagiat_doc_id' => (string) $result['doc_id'],
                'antiplagiat_percent' => $percent,
                'antiplagiat_report' => $report,
                'antiplagiat_checked_at' => now(),
            ])->save();

            $article->addProcessStep('antiplagiat_done', 'antiplagiat_done', [
                'percent' => $percent,
                'doc_id' => $result['doc_id'],
            ]);
        } catch (Throwable $e) {
            Log::error('Antiplagiat check failed', [
                'article_id' => $article->id,
                'message' => $e->getMessage(),
            ]);

            $article->refresh();
            $article->addProcessStep('antiplagiat_failed', 'antiplagiat_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    private function normalizeReport(array $result): array
    {
        $short = json_decode(json_encode($result['short_report'] ?? null), true);
        $full = json_decode(json_encode($result['full_report'] ?? null), true);

        $reportUrl = $this->findReportUrl(is_array($short) ? $short : [])
            ?? $this->findReportUrl(is_array($full) ? $full : []);

        return [
            'status' => $result['status'] ?? null,
            'short_report' => $short,
            'full_report' => $full,
            'report_url' => $reportUrl,
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function extractOriginalityPercent(array $report): ?float
    {
        $candidates = [
            data_get($report, 'short_report.Summary.Score'),
            data_get($report, 'short_report.Summary.Originality'),
            data_get($report, 'short_report.Score'),
            data_get($report, 'full_report.Summary.Score'),
            data_get($report, 'full_report.Summary.Originality'),
        ];

        foreach ($candidates as $value) {
            if (! is_numeric($value)) {
                continue;
            }

            $score = (float) $value;

            // API часто отдаёт долю 0..1
            if ($score >= 0 && $score <= 1) {
                return round($score * 100, 2);
            }

            return round($score, 2);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function findReportUrl(array $payload): ?string
    {
        foreach (['ReportLink', 'WebReportLink', 'Url', 'report_url', 'Link'] as $key) {
            $value = data_get($payload, $key)
                ?? data_get($payload, "Summary.{$key}")
                ?? data_get($payload, "Details.{$key}");

            if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
        }

        return null;
    }
}

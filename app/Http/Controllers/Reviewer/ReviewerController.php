<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Mail\EditorReviewsCompletedMail;
use App\Models\Article;
use App\Models\Review;
use App\Models\ReviewAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReviewerController extends Controller
{
    public function show(string $token)
    {
        $assignment = $this->findAssignment($token);

        if (in_array($assignment->status, ['completed', 'declined'], true)) {
            return view('pages.reviewer.completed', [
                'assignment' => $assignment,
                'mode' => $assignment->status,
            ]);
        }

        if ($assignment->status === 'invited') {
            $assignment->forceFill(['status' => 'accepted'])->save();
        }

        return view('pages.reviewer.show', [
            'assignment' => $assignment,
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $assignment = $this->findAssignment($token);

        if (in_array($assignment->status, ['completed', 'declined'], true)) {
            return redirect()
                ->route('reviewer.show', $token)
                ->with('error', 'Рецензия по этому приглашению уже закрыта');
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in(['accept', 'minor_revision', 'major_revision', 'reject'])],
            'author_comments' => ['required', 'string', 'min:10'],
            'editor_comments' => ['nullable', 'string'],
        ]);

        Review::query()->updateOrCreate(
            ['review_assignment_id' => $assignment->id],
            [
                'decision' => $data['decision'],
                'author_comments' => $data['author_comments'],
                'editor_comments' => $data['editor_comments'] ?? null,
                'submitted_at' => now(),
            ]
        );

        $assignment->forceFill(['status' => 'completed'])->save();

        $article = $assignment->article;
        $article->addProcessStep('review_received', 'review_received', [
            'assignment_id' => $assignment->id,
            'decision' => $data['decision'],
        ]);

        $this->maybeCompleteReviews($article);

        return redirect()
            ->route('reviewer.show', $token)
            ->with('success', 'Рецензия отправлена');
    }

    public function decline(Request $request, string $token)
    {
        $assignment = $this->findAssignment($token);

        if (in_array($assignment->status, ['completed', 'declined'], true)) {
            return redirect()->route('reviewer.show', $token);
        }

        $request->validate([
            'confirm' => ['accepted'],
        ]);

        $assignment->forceFill(['status' => 'declined'])->save();

        $article = $assignment->article;
        $article->addProcessStep('review_declined', 'review_declined', [
            'assignment_id' => $assignment->id,
        ]);

        $this->maybeCompleteReviews($article);

        return redirect()
            ->route('reviewer.show', $token)
            ->with('success', 'Вы отказались от рецензирования');
    }

    public function download(string $token): BinaryFileResponse
    {
        $assignment = $this->findAssignment($token);

        if (! filled($assignment->anonymized_file) || ! Storage::disk('local')->exists($assignment->anonymized_file)) {
            abort(404, 'Файл не найден');
        }

        $extension = pathinfo($assignment->anonymized_file, PATHINFO_EXTENSION) ?: 'bin';
        $absolutePath = Storage::disk('local')->path($assignment->anonymized_file);

        return response()->download($absolutePath, 'manuscript.' . $extension);
    }

    private function findAssignment(string $token): ReviewAssignment
    {
        return ReviewAssignment::query()
            ->with(['article', 'reviewer', 'review'])
            ->where('token', $token)
            ->firstOrFail();
    }

    private function maybeCompleteReviews(Article $article): void
    {
        $hasPending = $article->reviewAssignments()
            ->whereNotIn('status', ['completed', 'declined'])
            ->exists();

        if ($hasPending) {
            return;
        }

        if ($article->current_status !== 'review_completed') {
            $article->addProcessStep('review_completed', 'review_completed');
        }

        $article->load(['reviewAssignments.reviewer', 'reviewAssignments.review']);

        $editorEmail = config('mail.editor_address') ?: config('mail.from.address');

        if (filled($editorEmail)) {
            Mail::to($editorEmail)->send(new EditorReviewsCompletedMail($article));
        }
    }
}

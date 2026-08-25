@extends('layouts.reviewer')

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @if (($mode ?? $assignment->status) === 'declined')
            <h2 class="text-xl font-semibold text-custom-main">Отказ принят</h2>
            <p class="mt-3 text-slate-700">
                Вы отказались от рецензирования статьи
                «{{ $assignment->anonymized_title }}».
            </p>
        @else
            <h2 class="text-xl font-semibold text-custom-main">Рецензия отправлена</h2>
            <p class="mt-3 text-slate-700">
                Спасибо! Ваша рецензия по статье
                «{{ $assignment->anonymized_title }}» сохранена.
            </p>

            @if ($assignment->review)
                <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-500">Решение</dt>
                        <dd class="font-medium">{{ $assignment->review->decision }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Отправлено</dt>
                        <dd class="font-medium">{{ $assignment->review->submitted_at?->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
            @endif
        @endif
    </section>
@endsection

@extends('layouts.reviewer')

@section('content')
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-custom-main">{{ $assignment->anonymized_title }}</h2>

        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-slate-500">Дедлайн</dt>
                <dd class="font-medium">{{ $assignment->deadline?->format('d.m.Y') ?? 'не указан' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Статус</dt>
                <dd class="font-medium">{{ $assignment->status }}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <a href="{{ route('reviewer.download', $assignment->token) }}"
               class="inline-flex items-center rounded bg-custom-main px-4 py-2 text-sm font-semibold text-white hover:bg-custom-mainDark">
                Скачать рукопись
            </a>
        </div>
    </section>

    <section class="mt-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-custom-main">Форма рецензии</h3>

        <form method="POST" action="{{ route('reviewer.submit', $assignment->token) }}" class="mt-6 space-y-5">
            @csrf

            <fieldset>
                <legend class="mb-2 text-sm font-semibold">Решение</legend>
                <div class="space-y-2">
                    @foreach ([
                        'accept' => 'Принять',
                        'minor_revision' => 'Незначительная правка',
                        'major_revision' => 'Существенная правка',
                        'reject' => 'Отклонить',
                    ] as $value => $label)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio"
                                   name="decision"
                                   value="{{ $value }}"
                                   @checked(old('decision') === $value)
                                   required>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div>
                <label for="author_comments" class="mb-1 block text-sm font-semibold">Комментарии автору</label>
                <textarea id="author_comments"
                          name="author_comments"
                          rows="6"
                          required
                          class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-custom-main focus:outline-none">{{ old('author_comments') }}</textarea>
            </div>

            <div>
                <label for="editor_comments" class="mb-1 block text-sm font-semibold">Комментарии редактору (не видны автору)</label>
                <textarea id="editor_comments"
                          name="editor_comments"
                          rows="4"
                          class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-custom-main focus:outline-none">{{ old('editor_comments') }}</textarea>
            </div>

            <button type="submit"
                    class="rounded bg-custom-main px-4 py-2 text-sm font-semibold text-white hover:bg-custom-mainDark">
                Отправить рецензию
            </button>
        </form>
    </section>

    <section class="mt-8 rounded-lg border border-red-100 bg-red-50 p-6">
        <h3 class="text-lg font-semibold text-red-800">Отказ от рецензирования</h3>
        <p class="mt-2 text-sm text-red-700">Если вы не можете провести рецензию, подтвердите отказ.</p>

        <form method="POST" action="{{ route('reviewer.decline', $assignment->token) }}" class="mt-4"
              onsubmit="return confirm('Вы уверены, что хотите отказаться от рецензирования?');">
            @csrf
            <label class="mb-3 flex items-center gap-2 text-sm text-red-800">
                <input type="checkbox" name="confirm" value="1" required>
                <span>Подтверждаю отказ</span>
            </label>
            <button type="submit" class="rounded border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                Отказаться
            </button>
        </form>
    </section>
@endsection

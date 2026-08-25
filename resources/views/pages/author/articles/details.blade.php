@extends('layouts.public')
@section('content')
    <div>
        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap-reverse">
            <div class="flex-1 mr-0 md:mr-[30px]">
                <div>
                    <h1 class="text-3xl text-custom-main font-semibold border-b-2 border-b-custom-main pb-[10px]">{{ $article->title }}</h1>
                </div>
                <div class="mt-[30px] border-b border-b-custom-main pb-[10px]">
                    <div>
                        <div>
                            <div class="text-lg text-custom-main font-semibold">{{ $article->author->name }}</div>
                            <div class="text-md text-gray-600 mt-[12px]">{{ $article->author->organisation }}</div>
                        </div>
                    </div>
                    @foreach(($article->coauthors ?? []) as $author)
                        <div class="mt-[30px]">
                            <div>
                                <div class="text-lg text-custom-main font-semibold">{{ $author['fullname'] ?? '' }}</div>
                                <div class="text-md text-gray-600 mt-[12px]">{{ $author['organisation'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-[20px] border-b border-b-custom-main pb-[20px]">
                    <div>
                        <h3 class="text-lg text-custom-main font-semibold">Аннотация</h3>
                        <p class="mt-[15px]">{{ $article->annotation }}</p>
                    </div>
                </div>

                @if ($reviewsForAuthor->isNotEmpty())
                    <div class="mt-[30px] border-b border-b-custom-main pb-[20px]">
                        <h3 class="text-lg text-custom-main font-semibold">Комментарии рецензентов</h3>
                        @foreach ($reviewsForAuthor as $i => $review)
                            <div class="mt-[15px] rounded border border-slate-200 p-4">
                                <div class="font-semibold text-custom-main">Рецензия {{ $i + 1 }} — {{ $review->decision }}</div>
                                <p class="mt-2 whitespace-pre-line">{{ $review->author_comments }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($article->comments->isNotEmpty())
                    <div class="mt-[30px] border-b border-b-custom-main pb-[20px]">
                        <h3 class="text-lg text-custom-main font-semibold">Переписка с редактором</h3>
                        @foreach ($article->comments as $comment)
                            <div class="mt-[15px] rounded border border-slate-200 p-4">
                                <div class="text-sm text-gray-500">
                                    {{ $comment->sender_type === 'editor' ? 'Редактор' : 'Вы' }}
                                    · {{ $comment->created_at?->format('d.m.Y H:i') }}
                                </div>
                                <p class="mt-2 whitespace-pre-line">{{ $comment->message }}</p>
                                @if ($comment->attachment)
                                    <a class="mt-2 inline-block text-custom-main underline"
                                       href="{{ asset('storage/' . $comment->attachment) }}"
                                       target="_blank">Вложение</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($relayMessages->isNotEmpty())
                    <div class="mt-[30px] border-b border-b-custom-main pb-[20px]">
                        <h3 class="text-lg text-custom-main font-semibold">Переписка по email</h3>
                        @foreach ($relayMessages as $message)
                            <div class="mt-[15px] rounded border border-slate-200 p-4">
                                <div class="text-sm text-gray-500">
                                    {{ $message->sender_type === 'editor' ? 'Редакция' : 'Вы' }}
                                    · {{ $message->sent_at?->format('d.m.Y H:i') }}
                                </div>
                                <p class="mt-2 whitespace-pre-line">{{ $message->body }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (in_array($article->current_status, ['literary_editing', 'awaiting_author_revision'], true))
                    <div class="mt-[30px]">
                        <h3 class="text-lg text-custom-main font-semibold">Загрузить исправленную версию</h3>
                        <form method="POST" action="{{ route('author.article.revision', $article) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block font-semibold mb-1">Комментарий</label>
                                <textarea name="message" rows="4" required class="w-full border border-custom-main rounded p-3">{{ old('message') }}</textarea>
                            </div>
                            <div>
                                <label class="block font-semibold mb-1">Файл статьи</label>
                                <input type="file" name="file" accept=".pdf,.doc,.docx" required>
                            </div>
                            <button type="submit" class="bg-custom-main text-white font-semibold px-4 py-2 rounded">
                                Отправить правки
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="shrink-0 w-full md:w-[300px] mb-[30px] md:mb-0">
                <div class="border-2 border-custom-main rounded-lg p-[20px]">
                    <div class="text-lg font-semibold text-custom-main">Статус публикации статьи</div>
                    <div class="mt-[30px]">
                        <div class="flex items-center">
                            <div class="pt-[3px] mr-[5px] text-orange-600"><i class="ph-fill ph-circle"></i></div>
                            <div class="mr-[5px]">
                                {{ $article->current_status }}
                                @if (!empty($article->current_step['at']))
                                    — {{ $article->current_step['at'] }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

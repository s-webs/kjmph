@extends('layouts.public')
@section('content')
    <h1 class="text-3xl font-semibold text-custom-main mb-[20px]">{{ __('public.search') }}</h1>

    @php
        $articleTotal = $articles ? $articles->total() : 0;
        $nothing = $q !== '' && ! $unavailable && $articleTotal === 0
            && $releases->isEmpty() && $collections->isEmpty() && $authors->isEmpty();
    @endphp

    @if($unavailable)
        <p class="text-slate-600">{{ __('public.search_unavailable') }}</p>
    @elseif($q === '')
        <p class="text-slate-600">{{ __('public.search_enter_query') }}</p>
    @elseif($nothing)
        <p class="text-slate-600">{{ __('public.search_nothing_found') }}</p>
    @endif

    @if($authors->isNotEmpty())
        <section class="mb-[40px]">
            <h2 class="text-2xl border-b-2 border-custom-main text-custom-main pb-2 mb-4">{{ __('public.search_authors') }}</h2>
            @foreach($authors as $author)
                <div class="mb-4">
                    <div class="font-semibold text-custom-main">{{ $author->name }}</div>
                    @if($author->organisation)
                        <div class="text-sm text-slate-600">{{ $author->organisation }}</div>
                    @endif
                    <ul class="mt-1 list-disc pl-5 text-sm">
                        @foreach($author->publishedArticles as $item)
                            <li>
                                <a class="underline text-custom-main"
                                   href="{{ route('journal.release.show', $item->release_id) }}#article-{{ $item->id }}">{{ $item->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>
    @endif

    @if($articles && $articleTotal > 0)
        <section class="mb-[40px]">
            <h2 class="text-2xl border-b-2 border-custom-main text-custom-main pb-2 mb-4">
                {{ __('public.search_articles') }} ({{ $articleTotal }})
            </h2>
            @foreach($articles as $article)
                <article class="mb-6 border-b border-slate-200 pb-6">
                    <h3 class="text-xl font-semibold text-custom-main">
                        <a href="{{ route('journal.release.show', $article->release_id) }}#article-{{ $article->id }}">{{ $article->title }}</a>
                    </h3>
                    <div class="mt-2 text-sm text-slate-600">
                        {{ collect([$article->author?->name])->merge(collect($article->coauthors ?? [])->pluck('fullname'))->filter()->implode(', ') }}
                    </div>
                    @if($article->annotation)
                        <p class="mt-3 text-slate-700">{{ \Illuminate\Support\Str::limit(strip_tags((string) $article->annotation), 320) }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap gap-4 text-sm">
                        @if($article->release)
                            <a href="{{ route('journal.release.show', $article->release) }}" class="text-custom-main underline">{{ $article->release->title }}</a>
                        @endif
                        @if($article->doi)
                            <a href="https://doi.org/{{ ltrim($article->doi, '/') }}" target="_blank" class="text-custom-main underline">DOI: {{ $article->doi }}</a>
                        @endif
                        @if($article->file)
                            <a href="{{ asset($article->file) }}" target="_blank" class="text-custom-main underline">PDF</a>
                        @endif
                    </div>
                </article>
            @endforeach
            {{ $articles->links() }}
        </section>
    @endif

    @if($releases->isNotEmpty())
        <section class="mb-[40px]">
            <h2 class="text-2xl border-b-2 border-custom-main text-custom-main pb-2 mb-4">{{ __('public.search_releases') }}</h2>
            <ul class="list-disc pl-5">
                @foreach($releases as $release)
                    <li class="mb-1"><a class="underline text-custom-main" href="{{ route('journal.release.show', $release) }}">{{ $release->title }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($collections->isNotEmpty())
        <section class="mb-[40px]">
            <h2 class="text-2xl border-b-2 border-custom-main text-custom-main pb-2 mb-4">{{ __('public.search_collections') }}</h2>
            <ul class="list-disc pl-5">
                @foreach($collections as $collection)
                    <li class="mb-1">
                        <a class="underline text-custom-main" href="{{ route('collections.index') }}">{{ $collection->title }}</a>
                        @if($collection->file)
                            — <a class="underline text-custom-main" href="{{ asset($collection->file) }}" target="_blank">PDF</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection

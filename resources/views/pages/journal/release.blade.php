@extends('layouts.public')
@section('content')
    <div class="">
        <div class="flex flex-col md:flex-row items-center bg-custom-main p-[30px] rounded-xl">
            <div class="md:mr-[30px] shrink-0">
                <img src="/{{ $settings->cover }}" alt="" class="w-[270px]">
            </div>
            <div class="mt-[30px] md:mt-[0] w-full">
                <div class="text-3xl font-semibold text-white">
                    <h1>{{ $release->title }}</h1>
                </div>
                <div class="h-[3px] bg-white w-full my-[30px]"></div>
                <div class="text-lg font-semibold text-white">
                    <div class="mb-[10px] border-b border-b-custom-active md:border-none">
                        eISSN: <a href="{{ $settings->eissn_link }}"
                                  class="underline text-blue-300">{{ $settings->eissn }}</a>
                    </div>
                    <div class="mb-[10px] border-b border-b-custom-active md:border-none">
                        {{ __('public.abbreviation_name') }}: {{ $settings->abbreviation_name }}
                    </div>
                    <div class="mb-[10px] border-b border-b-custom-active md:border-none">
                        {{ $settings->year_start }}
                    </div>
                    <div class="mb-[10px] border-b border-b-custom-active md:border-none">
                        {{ __('public.frequency') }}: {{ $settings->frequency }}
                    </div>
                    <div class="mb-[10px] border-b border-b-custom-active md:border-none">
                        {{ __('public.languages') }}: {{ $settings->languages }}
                    </div>
                    <div class="border-b border-b-custom-active md:border-none">
                        {{ __('public.certificate_state_registration') }} <a
                            href="{{ $settings->certificate_state_registration_link }}"
                            class="underline text-blue-300">{{ $settings->certificate_state_registration }}</a>
                    </div>
                </div>
                <div class="h-[3px] bg-white w-full my-[30px]"></div>
                <div>
                    <a href="/{{ $release->file }}" class="flex font-semibold items-center justify-between text-white bg-custom-active p-[15px] rounded-lg">
                        <span>{{ __('public.download_full_issue') }}</span>
                        <i class="ph ph-file-arrow-down text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-[30px]">
        @forelse($articlesBySection as $sectionId => $articles)
            @php
                $section = $articles->first()?->sectionModel;
                $sectionName = $section?->name ?? $section?->name_ru ?? 'Раздел';
            @endphp
            <div class="mb-[40px]">
                <div class="text-2xl border-b-2 border-custom-main text-custom-main pb-2 mb-4">
                    <h3>{{ $sectionName }}</h3>
                </div>

                @foreach($articles as $article)
                    <article class="mb-6 border-b border-slate-200 pb-6">
                        <h4 class="text-xl font-semibold text-custom-main">{{ $article->title }}</h4>

                        <div class="mt-2 text-sm text-slate-600">
                            {{ $article->author?->name }}
                            @foreach(($article->coauthors ?? []) as $coauthor)
                                @if(!empty($coauthor['fullname']))
                                    , {{ $coauthor['fullname'] }}
                                @endif
                            @endforeach
                        </div>

                        @if($article->annotation)
                            <p class="mt-3 text-slate-700">{{ \Illuminate\Support\Str::limit(strip_tags((string) $article->annotation), 320) }}</p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-4 text-sm">
                            @if($article->doi)
                                <a href="https://doi.org/{{ ltrim($article->doi, '/') }}"
                                   target="_blank"
                                   class="text-custom-main underline">
                                    DOI: {{ $article->doi }}
                                </a>
                            @endif

                            @if($article->file)
                                <a href="{{ asset('storage/' . $article->file) }}"
                                   target="_blank"
                                   class="text-custom-main underline">
                                    PDF
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @empty
            <p class="text-slate-600">В этом выпуске пока нет опубликованных статей.</p>
        @endforelse
    </div>
@endsection

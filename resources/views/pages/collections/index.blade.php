@extends('layouts.public')
@section('content')
    <div class="">
        @forelse($items as $item)
            <div class="flex flex-col md:flex-row items-center bg-custom-main p-[30px] rounded-xl mb-[30px]">
                <div class="md:mr-[30px] shrink-0">
                    @if($item->cover)
                        <img src="{{ asset('storage/' . $item->cover) }}" alt="{{ $item->title }}" class="w-[220px]">
                    @endif
                </div>
                <div class="mt-[30px] md:mt-[0] w-full">
                    <div class="text-2xl font-semibold text-white">
                        <h2>{{ $item->title }}</h2>
                    </div>
                    <div class="h-[3px] bg-white w-full my-[10px]"></div>
                    <div class="text-md font-semibold text-white space-y-2">
                        @if($item->description)
                            <div class="font-normal opacity-90">{!! \Illuminate\Support\Str::limit(strip_tags((string) $item->description), 280) !!}</div>
                        @endif
                        <div>
                            @if($item->year) {{ $item->year }} @endif
                            @if($item->volume) · Том {{ $item->volume }} @endif
                            @if($item->number) · № {{ $item->number }} @endif
                        </div>
                        @if($item->doi)
                            <div>
                                DOI:
                                <a href="https://doi.org/{{ ltrim($item->doi, '/') }}"
                                   class="underline text-blue-300"
                                   target="_blank"
                                   rel="noopener">{{ $item->doi }}</a>
                            </div>
                        @endif
                    </div>
                    <div class="h-[3px] bg-white w-full my-[10px]"></div>
                    @php
                        $downloadUrl = $item->file
                            ? asset('storage/' . $item->file)
                            : ($item->url_id ?: null);
                    @endphp
                    @if($downloadUrl)
                        <div>
                            <a href="{{ $downloadUrl }}"
                               target="_blank"
                               rel="noopener"
                               class="flex font-semibold items-center justify-between text-white bg-custom-active p-[15px] rounded-lg">
                                <span>{{ __('public.download_full_issue') }}</span>
                                <i class="ph ph-file-arrow-down text-xl"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-custom-main">Сборники пока не опубликованы.</p>
        @endforelse
    </div>
@endsection

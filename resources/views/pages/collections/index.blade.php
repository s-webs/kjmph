@extends('layouts.public')
@section('content')
    <div class="">
        @foreach($items as $item)
            <div class="flex flex-col md:flex-row items-center bg-custom-main p-[30px] rounded-xl mb-[30px]">
                <div class="md:mr-[30px] shrink-0">
                    <img src="/{{ $settings->cover }}" alt="" class="w-[220px]">
                </div>
                <div class="mt-[30px] md:mt-[0] w-full">
                    <div class="text-2xl font-semibold text-white">
                        <h1>{{ $item->title }}</h1>
                    </div>
                    <div class="h-[3px] bg-white w-full my-[10px]"></div>
                    <div class="text-md font-semibold text-white">
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
                    <div class="h-[3px] bg-white w-full my-[10px]"></div>
                    <div>
                        <a href="/{{ $item->file }}" class="flex font-semibold items-center justify-between text-white bg-custom-active p-[15px] rounded-lg">
                            <span>{{ __('public.download_full_issue') }}</span>
                            <i class="ph ph-file-arrow-down text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

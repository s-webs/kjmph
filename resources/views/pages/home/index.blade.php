@extends('layouts.public')
@section('content')
    <div class="">
        <div class="flex flex-col md:flex-row items-center bg-custom-main p-[30px] rounded-xl">
            <div class="md:mr-[30px] shrink-0">
                <img src="/{{ $settings->cover }}" alt="" class="w-[270px]">
            </div>
            <div class="mt-[30px] md:mt-[0]">
                <div class="text-lg font-semibold text-white">
                    {{ $settings->description }}
                </div>
                <div class="h-[3px] bg-white w-full my-[30px]"></div>
                <div class="text-lg font-semibold text-white">
                    <div class="mb-[20px] border-b border-b-custom-active md:border-none">
                        eISSN: <a href="{{ $settings->eissn_link }}" class="underline text-blue-300">{{ $settings->eissn }}</a>
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
                        {{ __('public.certificate_state_registration') }} <a href="{{ $settings->certificate_state_registration_link }}" class="underline text-blue-300">{{ $settings->certificate_state_registration }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-[30px]">
        {!! $page->text !!}
    </div>
@endsection

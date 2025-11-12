@php
    $labels = ['en' => 'ENG', 'ru' => 'РУС', 'kk' => 'ҚАЗ'];
@endphp

<header class="container mx-auto px-4  mt-[30px]">
    <div class="flex bg-custom-main items-center justify-between p-[20px] rounded-xl">
        <div>
            <a href="#" class="flex items-center">
                <img src="/{{ $settings->logo }}" alt="{{ $settings->name }}" class="w-[60px] mr-[20px]">
                <span
                    class="text-xl lg:w-[370px] text-white font-semibold hover:text-custom-active transition-colors duration-300">{{ $settings->name }}</span>
            </a>
        </div>
        <div class="flex items-center text-white font-semibold text-lg">
            <div class="mr-[20px]">
                <a href="##" class="hover:text-custom-active transition-colors duration-300">{{ __('public.login') }}</a>
            </div>
            <div>
                <a href="##" class="hover:text-custom-active transition-colors duration-300">{{ __('public.register') }}</a>
            </div>
        </div>
    </div>
    <div class="mt-[30px] bg-white border-[2px] border-custom-main rounded-xl px-[20px] py-[15px]">
        <div class="flex items-center justify-between">
            <div class="">
                {{-- {{ dd($menuTree) }} --}}
                <x-navbar :items="$menuTree"/>
            </div>
            <div class="uppercase font-semibold text-lg text-custom-main">
                @foreach($labels as $loc => $label)
                    <a href="{{ localized_switch_url($loc) }}"
                       class="mr-[10px] hover:text-custom-active {{ app()->getLocale()===$loc ? 'underline' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>
</header>

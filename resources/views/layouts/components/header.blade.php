@php
    $labels = ['en' => 'ENG', 'ru' => 'РУС', 'kk' => 'ҚАЗ'];
@endphp

<header x-data="{ open:false }" class="container mx-auto px-4 mt-[30px]">
    {{-- Верхняя цветная плашка --}}
    <div class="flex bg-custom-main items-center justify-between p-[20px] rounded-xl">
        <div>
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="/{{ $settings->logo }}" alt="{{ $settings->name }}" class="w-[60px] mr-[20px]">
                <span
                    class="text-md lg:text-xl lg:w-[370px] text-white font-semibold hover:text-custom-active transition-colors duration-300">
                    {{ $settings->name }}
                </span>
            </a>
        </div>


        {{-- Кнопки вход/регистрация на десктопе --}}
        <div class="hidden lg:flex items-center text-white font-semibold text-lg">
            @auth
                <div class="mr-[20px]">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="hover:text-custom-active transition-colors duration-300 cursor-pointer">
                            {{ __('auth.logout') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="mr-[20px]">
                    <a href="{{ route('login') }}" class="hover:text-custom-active transition-colors duration-300">
                        {{ __('public.login') }}
                    </a>
                </div>
                <div>
                    <a href="{{ route('register') }}" class="hover:text-custom-active transition-colors duration-300">
                        {{ __('public.register') }}
                    </a>
                </div>
            @endauth
        </div>

        {{-- Бургер для планшетов и смартфонов --}}
        <button
            @click="open = true"
            class="lg:hidden shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg border border-white/40 text-white hover:bg-white/10 transition"
            aria-label="Open menu"
        >
            <i class="ph ph-list text-2xl"></i>
        </button>
    </div>

    <div class="mt-[30px] bg-white border-[2px] border-custom-main rounded-xl px-[20px] py-[0] hidden lg:block">
        <div class="flex items-center justify-between">
            <div>
                <x-navbar :items="$menuTree"/>
            </div>
            <div class="uppercase font-semibold text-lg text-custom-main">
                @foreach($labels as $loc => $label)
                    <a href="{{ localized_switch_url($loc) }}"
                       class="mr-[10px] hover:text-custom-active {{ app()->getLocale()===$loc ? 'underline' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @auth
        <div class="mt-[30px]">
            <div class="mt-[30px] bg-white border-[2px] border-custom-main rounded-xl px-[20px] py-[0] hidden lg:block">
                <div class="py-[15px]">
                    <div>
                        <a href="{{ route('author.articles.index') }}" class="text-custom-main font-semibold">{{ __('public.myarticles') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    <div>
        <div
            x-cloak
            x-show="open"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 bg-black/40 z-40"
            @click="open = false">
        </div>
        <nav
            x-cloak
            x-show="open"
            @keydown.escape.window="open = false"
            class="fixed inset-y-0 left-0 w-[280px] max-w-full bg-white z-50 shadow-2xl p-5 flex flex-col"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <div class="flex items-center justify-between bg-custom-main p-2 rounded-lg">
                <div class="uppercase font-semibold text-sm text-white">
                    @foreach($labels as $loc => $label)
                        <a href="{{ localized_switch_url($loc) }}"
                           class="mr-[10px] hover:text-custom-active {{ app()->getLocale()===$loc ? 'underline' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <button
                    @click="open = false"
                    class="inline-flex items-center text-white justify-center w-8 h-8 rounded-lg"
                    aria-label="Закрыть меню"
                >
                    <i class="ph ph-x-circle text-2xl"></i>
                </button>
            </div>

            <div class="flex items-center justify-between mb-6 mt-[20px]">
                <a href="#" class="flex items-center">
                    <img src="/{{ $settings->logo }}" alt="{{ $settings->name }}" class="w-[40px] mr-[10px]">
                    <span class="text-base font-semibold text-custom-main">
                {{ $settings->name }}
            </span>
                </a>
            </div>

            <div class="space-y-2 mb-4">
                <a href="##"
                   class="block w-full text-left p-2 font-semibold text-custom-main hover:text-custom-active bg-custom-main text-white rounded-lg">
                    {{ __('public.login') }}
                </a>
                <a href="##"
                   class="block w-full text-left p-2 font-semibold text-custom-main hover:text-custom-active bg-custom-main text-white rounded-lg">
                    {{ __('public.register') }}
                </a>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="pb-4 mb-4 border-b border-gray-200">
                    <x-mobile-navbar :items="$menuTree"/>
                </div>
            </div>
        </nav>
    </div>

</header>

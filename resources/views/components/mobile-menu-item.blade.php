@props(['item', 'level' => 0])

@php
    // Тут подстрой под свою структуру:
    $children = $item->children ?? collect();   // или $item->children ?? []
    $hasChildren = $children && count($children);
    $isRoot = $level === 0;
@endphp

<li
    x-data="{ open: false }"
    class="relative {{ $isRoot ? '' : 'pl-4 border-l border-gray-200' }}"
>
    <div class="flex items-center justify-between">
        {{-- Ссылка на пункт меню --}}
        <a
            href="{{ $item->url ?? '#' }}" {{-- тут подставь свой путь --}}
        class="py-2 text-sm w-full text-left
                   {{ $isRoot ? 'font-semibold text-custom-main' : 'text-gray-700' }}"
        >
            {{ $item->title ?? 'Без названия' }} {{-- подставь своё поле --}}
        </a>

        {{-- Кнопка раскрытия подменю --}}
        @if($hasChildren)
            <button
                type="button"
                @click.stop="open = !open"
                class="ml-2 p-1 shrink-0"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 transition-transform duration-200"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     :class="{ 'rotate-90': open }"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Подменю --}}
    @if($hasChildren)
        <ul
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="mt-1 space-y-1 pl-[10px] list-none"
        >
            @foreach($children as $child)
                <x-mobile-menu-item :item="$child" :level="$level + 1" />
            @endforeach
        </ul>
    @endif
</li>

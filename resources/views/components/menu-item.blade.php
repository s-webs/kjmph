@props(['item', 'level' => 0])

@php
    $hasChildren = $item->relationLoaded('childrenRecursive') && $item->childrenRecursive->isNotEmpty();
    // Позиция вложенного подменю: для уровня 0 — под пунктом, для остальных — справа
    $submenuPos = $level === 0 ? 'left-0 top-full' : 'left-full top-0';
@endphp

<li class="relative group">
    <a href="{{ $item->url ?? '#' }}"
       class="text-custom-main hover:text-custom-active transition-colors duration-300 font-semibold
              {{ $level === 0 ? 'text-lg' : 'text-base' }} flex items-center gap-1 py-2">
        <span>{{ $item->title }}</span>

        @if($hasChildren)
            {{-- стрелка вниз для корня, вправо для вложенных --}}
            @if($level === 0)
                <i class="ph ph-caret-down"></i>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70" viewBox="0 0 24 24"
                     fill="currentColor">
                    <path d="M9 6l6 6-6 6V6z"/>
                </svg>
            @endif
        @endif
    </a>

    @if($hasChildren)
        <ul class="absolute {{ $submenuPos }} hidden group-hover:block focus-within:block
                   min-w-56 bg-white shadow-xl rounded-xl py-2 z-50 list-none pl-0">
            @foreach($item->childrenRecursive as $child)
                <li class="relative group">
                    <a href="{{ $child->url ?? '#' }}"
                       class="block px-4 py-2 text-sm text-custom-main hover:text-custom-active hover:bg-gray-50
                              transition-colors duration-200 font-medium">
                        <span class="flex items-center gap-1">
                            {{ $child->title }}
                            @if(($child->childrenRecursive ?? collect())->isNotEmpty())
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70 ml-auto"
                                     viewBox="0 0 24 24" fill="currentColor"><path d="M9 6l6 6-6 6V6z"/></svg>
                            @endif
                        </span>
                    </a>

                    {{-- Рекурсивный вывод под-уровней --}}
                    @if(($child->childrenRecursive ?? collect())->isNotEmpty())
                        <ul class="absolute left-full top-0 hidden group-hover:block focus-within:block
                                   min-w-56 bg-white shadow-xl rounded-xl py-2 z-50">
                            @foreach($child->childrenRecursive as $grand)
                                <x-menu-item :item="$grand" :level="$level + 2"/>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</li>

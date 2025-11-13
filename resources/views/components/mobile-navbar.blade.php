@props(['items'])

<nav class="relative">
    <ul class="flex text-sm flex-col gap-1 lg:flex-row lg:items-center lg:gap-5 list-none pl-0">
        @foreach ($items as $item)
            <x-mobile-menu-item :item="$item" :level="0" />
        @endforeach
    </ul>
</nav>

@props(['items'])

<nav class="relative">
    <ul class="flex items-center gap-5">
        @foreach ($items as $item)
            <x-menu-item :item="$item" :level="0" />
        @endforeach
    </ul>
</nav>

<div class="bg-white p-6 rounded-xl shadow-sm space-y-6">
    @if($title)
        <h2 class="text-xl font-semibold text-custom-main">
            {{ $title }}
        </h2>
    @endif

    {{ $slot }}
</div>

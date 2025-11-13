<footer class="container mx-auto px-4 my-[30px]">
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
    </div>
</footer>

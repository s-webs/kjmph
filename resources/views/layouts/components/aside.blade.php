<div>
    <div class="text-xl font-bold mb-[30px] text-custom-main border-b-2 ">
        INDEXING/LISTING
    </div>
    <div class="flex flex-wrap items-center flex-row lg:flex-col">
        @foreach($indexers as $indexer)
            <div class="mb-[30px] mx-0 md:mx-[15px] lg:mx-[0px] w-full md:w-auto lg:w-full">
                <a href="{{ $indexer->link }}" class="" target="_blank">
                    <img src="/{{ $indexer->image }}" alt="{{ $indexer->name }}" class="w-full">
                </a>
            </div>
        @endforeach
    </div>
</div>

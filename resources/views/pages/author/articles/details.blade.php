@extends('layouts.public')
@section('content')
    <div>
        <div class="flex flex-wrap-reverse">
            <div class="flex-1 mr-0 md:mr-[30px]">
                <div>
                    <h1 class="text-3xl text-custom-main font-semibold border-b-2 border-b-custom-main pb-[10px]">{{ $article->title }}</h1>
                </div>
                <div class="mt-[30px] border-b border-b-custom-main pb-[10px]">
                    <div>
                        <div>
                            <div class="text-lg text-custom-main font-semibold">{{ $article->author->name }}</div>
                            <div class="text-md text-gray-600 mt-[12px]">{{ $article->author->organisation }}</div>
                            <div class="flex items-center mt-[12px]">
                                <div class="mr-[5px]"><img src="https://i0.wp.com/info.orcid.org/wp-content/uploads/2021/12/orcid_16x16.gif?resize=16%2C16&ssl=1" alt="orcid"></div>
                                <div class=""><a href="##" target="_blank">https://</a></div>
                            </div>
                        </div>
                    </div>
                    @foreach($article->coauthors as $author)
                        <div class="mt-[30px]">
                            <div>
                                <div class="text-lg text-custom-main font-semibold">{{ $author['fullname'] }}</div>
                                <div class="text-md text-gray-600 mt-[12px]">{{ $author['organisation'] }}</div>
                                <div class="flex items-center mt-[12px]">
                                    <div class="mr-[5px]"><img src="https://i0.wp.com/info.orcid.org/wp-content/uploads/2021/12/orcid_16x16.gif?resize=16%2C16&ssl=1" alt="orcid"></div>
                                    <div class=""><a href="{{ $author['orcid'] }}" target="_blank">{{ $author['orcid'] }}</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-[20px] border-b border-b-custom-main pb-[20px]">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/DOI_logo.svg/2048px-DOI_logo.svg.png" alt="" class="w-[24px]">
                </div>
                <div class="mt-[20px] border-b border-b-custom-main pb-[20px]">
                    <p>
                        <strong>Ключевые слова: </strong>
                    </p>
                </div>
                <div class="mt-[20px] border-b border-b-custom-main pb-[20px]">
                    <div class="">
                        <h3 class="text-lg text-custom-main font-semibold">Аннотация</h3>
                        <p class="mt-[15px]">{{ $article->annotation }}</p>
                    </div>
                </div>
                <div class="mt-[20px]">
                    <div class="">
                        <h3 class="text-lg text-custom-main font-semibold">Литература</h3>
                        <p class="mt-[15px]">{{ $article->literature }}</p>
                    </div>
                </div>
            </div>
            <div class="shrink-0 w-full md:w-[300px] mb-[30px] md:mb-0">
                <div class="border-2 border-custom-main rounded-lg p-[20px]">
                    <div class="text-lg font-semibold text-custom-main">Статус публикации статьи</div>
                    <div class="mt-[30px]">
                        <div class="flex items-center">
                            <div class="pt-[3px] mr-[5px] text-orange-600"><i class="ph-fill ph-circle"></i></div>
                            <div class="mr-[5px]">{{ __('public.' . $article->current_status) }} - {{ $article->current_step['at'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

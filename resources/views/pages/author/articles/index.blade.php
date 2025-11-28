@extends('layouts.public')
@section('content')
    <div class="mt-[30px]">
        <div>
            <div class="border-b-2 border-b-gray-300 pb-[20px] flex justify-end">
                <div>
                    <a href="{{ route('author.article.create') }}"
                       class="bg-green-600 hover:bg-green-700 px-[15px] py-[10px] font-semibold text-white rounded-sm">{{ __('public.submit_new_article') }}</a>
                </div>
            </div>
        </div>
        @foreach($articles as $article)
            <div class="mt-[30px]">
                <div class="flex items-center border-2 border-custom-main p-[15px] rounded-lg">
                    <div class="mr-[30px] shrink-0">
                        <img class="w-[100px] h-[150px] object-cover border"
                             src="/{{ $article->cover }}"
                             alt="">
                    </div>
                    <div class="w-full">
                        <div>
                            <a href="{{ route('author.article.details', $article) }}" class="text-lg font-semibold">{{ $article->title }}</a>
                        </div>
                        <div class="mt-[30px]">
                            <div>
                                <div class="flex items-center">
                                    <div class="mr-[5px]"><img src="https://i0.wp.com/info.orcid.org/wp-content/uploads/2021/12/orcid_16x16.gif?resize=16%2C16&ssl=1" alt="orcid"></div>
                                    <div class=""><a href="##" target="_blank">{{ $article->author->name }}</a></div>
                                </div>
                                @foreach($article->coauthors as $coauthor)
                                    <div class="flex items-center">
                                        <div class="mr-[5px]"><img src="https://i0.wp.com/info.orcid.org/wp-content/uploads/2021/12/orcid_16x16.gif?resize=16%2C16&ssl=1" alt="orcid"></div>
                                        <div class=""><a href="{{ $coauthor['orcid'] }}" target="_blank">{{ $coauthor['fullname'] }}</a></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-[20px] pt-4 border-t flex flex-wrap justify-between items-center border-gray-100 w-full">
                            <div class="flex items-center font-semibold">
                                <div class="mr-[10px]">{{ __('public.submission_status') }}:</div>
                                <div class="pt-[5px] mr-[5px] text-orange-600"><i class="ph-fill ph-circle"></i></div>
                                <div>{{ __('public.' . $article->current_status) }}</div>
                            </div>
                            <div class="mt-[20px] md:mt-0">
                                <a href="{{ route('author.article.details', $article) }}"
                                   class="inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-custom-main hover:bg-custom-mainDark focus:outline-none focus:ring-2 focus:ring-custom-mainDark focus:ring-offset-1">{{ __('public.details') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

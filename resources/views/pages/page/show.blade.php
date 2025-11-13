@extends('layouts.public')
@section('content')
    <div class="">
        <h1 class="text-4xl font-semibold text-custom-main border-b-4 border-b-custom-main pb-[10px]">{{ $page->name }}</h1>
    </div>
    <div class="mt-[30px] text-lg">
        {!! $page->text !!}
    </div>
@endsection

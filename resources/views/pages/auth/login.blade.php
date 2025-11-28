@extends('layouts.public')
@section('content')
    <div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            @method('POST')
            <x-input-block
                name="email"
                label="E-mail"
                required
            />
            <x-input-block
                name="password"
                label="{{ __('auth.password_field') }}"
                required
                type="password"
            />

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <button
                    type="submit"
                    class="inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-custom-main hover:bg-custom-mainDark focus:outline-none focus:ring-2 focus:ring-custom-mainDark focus:ring-offset-1"
                >
                    {{ __('public.login') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.public')

@section('content')
    <div class="w-full">
        {{-- Сообщение об успешном сохранении --}}
        @if(session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="w-full bg-white shadow-sm rounded-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ __('public.profile_editing') }}
                </h1>
                <div class="text-xs text-gray-400">
                    <p>{{ __('public.updated') }}: {{ $user->updated_at }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6 mt-[30px]">
                @csrf
                @method('POST')

                {{-- Имя --}}
                <x-input-block
                    name="name"
                    label="{{ __('auth.fullname') }}"
                    :value="$user->name"
                    required
                />

                {{-- Организация --}}
                <x-input-block
                    name="organisation"
                    label="{{ __('auth.organisation') }}"
                    :value="$user->organisation"
                    required
                />

                {{-- Страна --}}
                <x-input-block
                    name="country"
                    label="{{ __('auth.country') }}"
                    :value="$user->country"
                    required
                />

                {{-- Email --}}
                <x-input-block
                    name="email"
                    label="E-mail"
                    :value="$user->email"
                    required
                />

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button
                        type="submit"
                        class="inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-custom-main hover:bg-custom-mainDark focus:outline-none focus:ring-2 focus:ring-custom-mainDark focus:ring-offset-1"
                    >
                        {{ __('public.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.public')

@section('content')
    <div class="">
        <div class="mb-8">
            <h1 class="text-4xl font-semibold text-custom-main border-b-4 border-b-custom-main pb-[10px]">
                {{ __('public.submission_article') }}
            </h1>
        </div>

        <form
            action="{{ route('author.article.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            {{-- Общая информация --}}
            <x-form-section :title="__('public.main_information')">
                <div class="">
                    {{-- Раздел --}}
                    <x-select-block
                        name="section"
                        label="{{ __('public.select_journal_section') }}"
                        :options="$sections"
                        option-value="id"
                        option-label="name"
                        placeholder="{{ __('public.select_journal_section') }}"
                        :selected="old('section')"
                    />

                    {{-- Язык материала --}}
                    <x-select-block
                        name="material_lang"
                        label="{{ __('public.material_lang') }}"
                        :options="['en' => 'English', 'ru' => 'Русский', 'kk' => 'Қазақша']"
                        placeholder="{{ __('public.material_lang') }}"
                        :selected="old('material_lang')"
                    />

                    <div class="grid md:grid-cols-2 gap-6">
                        <x-file-input-block
                            name="cover"
                            label="{{ __('public.cover_illustration') }}"
                        />

                        <x-file-input-block
                            name="file"
                            label="{{ __('public.article_file') }} (DOCX)"
                        />
                    </div>
                </div>
            </x-form-section>

            {{-- Соавторы --}}
            <x-coauthors-block
                :initial="old('coauthors', [])"
                title="{{ __('public.coauthors') }}"
                help-text="{{ __('public.write_coauthors_article') }}"
            />


            {{-- Блок: Английский --}}
            <x-form-section title="{{ __('public.english_version') }}">
                <x-input-block
                    name="title_en"
                    label="{{ __('public.title') }} (EN)"
                    type="text"
                />

                <x-input-block
                    name="subtitle_en"
                    label="{{ __('public.subtitle') }} (EN)"
                    type="text"
                />

                <x-textarea-block
                    name="annotation_en"
                    label="{{ __('public.annotation') }} (EN)"
                    rows="5"
                />

                <x-textarea-block
                    name="literature_en"
                    label="{{ __('public.literature_list') }} (EN)"
                    rows="5"
                />
            </x-form-section>

            {{-- Блок: Русский --}}
            <x-form-section title="{{ __('public.russian_version') }}">
                <x-input-block
                    name="title_ru"
                    label="{{ __('public.title') }} (RU)"
                    type="text"
                />

                <x-input-block
                    name="subtitle_ru"
                    label="{{ __('public.subtitle') }} (RU)"
                    type="text"
                />

                <x-textarea-block
                    name="annotation_ru"
                    label="{{ __('public.annotation') }} (RU)"
                    rows="5"
                />

                <x-textarea-block
                    name="literature_ru"
                    label="{{ __('public.literature_list') }} (RU)"
                    rows="5"
                />
            </x-form-section>

            {{-- Блок: Казахский --}}
            <x-form-section title="{{ __('public.kazakh_version') }}">
                <x-input-block
                    name="title_kk"
                    label="{{ __('public.title') }} (KK)"
                    type="text"
                />

                <x-input-block
                    name="subtitle_kk"
                    label="{{ __('public.subtitle') }} (KK)"
                    type="text"
                />

                <x-textarea-block
                    name="annotation_kk"
                    label="{{ __('public.annotation') }} (KK)"
                    rows="5"
                />

                <x-textarea-block
                    name="literature_kk"
                    label="{{ __('public.literature_list') }} (KK)"
                    rows="5"
                />
            </x-form-section>

            {{-- Кнопка отправки --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-custom-main hover:bg-custom-active focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-main"
                >
                    {{ __('public.send_article') }}
                </button>
            </div>
        </form>
    </div>
@endsection

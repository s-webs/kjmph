<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Общая информация
            'section' => ['required', 'integer', 'exists:sections,id'],
            'material_lang' => ['required', 'in:en,ru,kk'],

            // Файлы
            'cover' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2 МБ
            ],
            'file' => [
                'required',
                'file',
                'mimes:docx,pdf',
                'max:10240', // 10 МБ
            ],

            // Соавторы (могут отсутствовать)
            'coauthors' => ['nullable', 'array'],
            'coauthors.*.role' => ['required_with:coauthors.*.fullname', 'string', 'max:100'],
            'coauthors.*.fullname' => ['required_with:coauthors.*.role', 'string', 'max:255'],
            'coauthors.*.organisation' => ['nullable', 'string', 'max:255'],
            'coauthors.*.orcid' => ['nullable', 'string', 'max:50'],

            // Английская версия
            'title_en' => ['nullable', 'string', 'max:255', 'required_if:material_lang,en'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'annotation_en' => ['nullable', 'string'],
            'literature_en' => ['nullable', 'string'],

            // Русская версия
            'title_ru' => ['nullable', 'string', 'max:255', 'required_if:material_lang,ru'],
            'subtitle_ru' => ['nullable', 'string', 'max:255'],
            'annotation_ru' => ['nullable', 'string'],
            'literature_ru' => ['nullable', 'string'],

            // Казахская версия
            'title_kk' => ['nullable', 'string', 'max:255', 'required_if:material_lang,kk'],
            'subtitle_kk' => ['nullable', 'string', 'max:255'],
            'annotation_kk' => ['nullable', 'string'],
            'literature_kk' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'section' => __('public.journal_section'),
            'material_lang' => __('public.material_lang'),
            'cover' => __('public.cover_illustration'),
            'file' => __('public.article_file'),

            'title_en' => __('public.title') . ' (EN)',
            'subtitle_en' => __('public.subtitle') . ' (EN)',
            'annotation_en' => __('public.annotation') . ' (EN)',
            'literature_en' => __('public.literature_list') . ' (EN)',

            'title_ru' => __('public.title') . ' (RU)',
            'subtitle_ru' => __('public.subtitle') . ' (RU)',
            'annotation_ru' => __('public.annotation') . ' (RU)',
            'literature_ru' => __('public.literature_list') . ' (RU)',

            'title_kk' => __('public.title') . ' (KK)',
            'subtitle_kk' => __('public.subtitle') . ' (KK)',
            'annotation_kk' => __('public.annotation') . ' (KK)',
            'literature_kk' => __('public.literature_list') . ' (KK)',
        ];
    }
}

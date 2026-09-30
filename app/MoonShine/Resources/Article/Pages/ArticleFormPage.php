<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Article\Pages;

use App\Models\Article;
use App\Models\Release;
use App\Models\Section;
use App\Models\User;
use App\MoonShine\Resources\Article\ArticleResource;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Throwable;

/**
 * @extends FormPage<ArticleResource>
 */
class ArticleFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Box::make('Публикация', [
                Select::make('Автор', 'author_id')
                    ->options(static fn (): array => User::query()->orderBy('name')->get()
                        ->mapWithKeys(static fn (User $user): array => [$user->id => $user->name . ' (' . $user->email . ')'])->all())
                    ->searchable()->required(),
                Select::make('Выпуск', 'release_id')
                    ->options(static fn (): array => Release::query()->orderByDesc('year')->orderByDesc('number')->get()
                        ->mapWithKeys(static fn (Release $release): array => [
                            $release->id => trim(($release->title_ru ?? $release->title_en ?? '#' . $release->id)
                                . ' · ' . $release->year . ' №' . $release->number),
                        ])->all())
                    ->searchable()->nullable(),
                Select::make('Раздел', 'section')
                    ->options(static fn (): array => Section::query()->orderBy('id')->get()
                        ->mapWithKeys(static fn (Section $section): array => [
                            $section->id => $section->name_ru ?? $section->name_en ?? '#' . $section->id,
                        ])->all())
                    ->searchable()->required(),
                Select::make('Язык материала', 'material_lang')
                    ->options(['ru' => 'Русский', 'en' => 'English', 'kk' => 'Қазақша'])
                    ->required(),
                Text::make('DOI', 'doi'),
                Divider::make(),
                Image::make('Обложка', 'cover')
                    ->disk('public')->dir('articles/covers')
                    ->allowedExtensions(['jpg', 'jpeg', 'png', 'webp'])
                    ->disableDeleteFiles(),
                File::make('Файл статьи', 'file')
                    ->disk('public')
                    ->dir('articles/files')
                    ->allowedExtensions(['pdf', 'docx'])
                    ->disableDeleteFiles(),
            ]),
            Box::make('Название и содержание', [
                Text::make('Название RU', 'title_ru'),
                Text::make('Название EN', 'title_en'),
                Text::make('Название KK', 'title_kk'),
                Text::make('Подзаголовок RU', 'subtitle_ru'),
                Text::make('Подзаголовок EN', 'subtitle_en'),
                Text::make('Подзаголовок KK', 'subtitle_kk'),
                Textarea::make('Аннотация RU', 'annotation_ru'),
                Textarea::make('Аннотация EN', 'annotation_en'),
                Textarea::make('Аннотация KK', 'annotation_kk'),
                Textarea::make('Литература RU', 'literature_ru'),
                Textarea::make('Литература EN', 'literature_en'),
                Textarea::make('Литература KK', 'literature_kk'),
            ]),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'author_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'release_id' => ['nullable', 'integer', Rule::exists('releases', 'id')],
            'section' => ['required', 'integer', Rule::exists('sections', 'id')],
            'material_lang' => ['required', Rule::in(['ru', 'en', 'kk'])],
            'doi' => ['nullable', 'string', 'max:255'],
            'title_ru' => ['nullable', 'string', 'max:255', 'required_if:material_lang,ru'],
            'title_en' => ['nullable', 'string', 'max:255', 'required_if:material_lang,en'],
            'title_kk' => ['nullable', 'string', 'max:255', 'required_if:material_lang,kk'],
            'file' => [$item->getKey() === null ? 'required' : 'nullable', 'file', 'mimes:pdf,docx', 'max:40960'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}

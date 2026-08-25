<x-mail::message>
# Статья опубликована

Здравствуйте{{ $authorName ? ', ' . $authorName : '' }}!

Ваша статья **{{ $title }}** опубликована.

@if($doiUrl)
**DOI:** [{{ $doi }}]({{ $doiUrl }})
@endif

Посмотреть выпуск:

<x-mail::button :url="$releaseUrl">
Открыть выпуск
</x-mail::button>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>

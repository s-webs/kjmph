<x-mail::message>
# Приглашение на рецензирование

Здравствуйте{{ $reviewerName ? ', ' . $reviewerName : '' }}!

Вас пригласили выступить рецензентом научной статьи.

**Название:** {{ $title }}

@if($deadline)
**Дедлайн:** {{ $deadline }}
@endif

Перейдите по ссылке, чтобы скачать материалы и отправить рецензию:

<x-mail::button :url="$portalUrl">
Открыть портал рецензента
</x-mail::button>

Или скопируйте ссылку:<br>
{{ $portalUrl }}

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>

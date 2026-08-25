<x-mail::message>
# Требуется литературная правка

Здравствуйте{{ $authorName ? ', ' . $authorName : '' }}!

По вашей статье **{{ $title }}** редактор запросил правки.

**Комментарий редактора:**

{{ $editorMessage }}

Перейдите в кабинет автора, чтобы ответить и загрузить исправленный файл:

<x-mail::button :url="$detailsUrl">
Открыть статью
</x-mail::button>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>

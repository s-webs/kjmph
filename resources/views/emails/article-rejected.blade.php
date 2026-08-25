<x-mail::message>
# Статья отклонена

Здравствуйте{{ $authorName ? ', ' . $authorName : '' }}!

К сожалению, статья **{{ $title }}** отклонена.

**Обоснование:**

{{ $reason }}

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>

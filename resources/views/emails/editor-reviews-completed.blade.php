<x-mail::message>
# Рецензирование завершено

По статье **#{{ $articleId }}** получены ответы всех рецензентов.

**Название:** {{ $title }}

@foreach($assignments as $assignment)
- {{ $assignment->reviewer?->name }} — **{{ $assignment->status }}**@if($assignment->review) ({{ $assignment->review->decision }})@endif

@endforeach

Проверьте статью в панели редактора.

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>

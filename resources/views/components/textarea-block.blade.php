@php
    $fieldId = $id ?? $name;
@endphp

<div>
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
    </label>

    <textarea
        id="{{ $fieldId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm'
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

@php
    $fieldId = $id ?? $name;
    $inputName = $multiple ? $name . '[]' : $name;
@endphp

<div>
    <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
    </label>

    <input
        type="file"
        id="{{ $fieldId }}"
        name="{{ $inputName }}"
        @if($required) required @endif
        @if($multiple) multiple @endif
        {{ $attributes->merge([
            'class' => 'block w-full text-sm text-gray-700
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100 mt-[15px]'
        ]) }}
    >

    @error($name)
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror

    @if($multiple)
        @error($name . '.*')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>

@php
    $fieldId = $id ?? $name;
    $current = old($name, $selected);
@endphp

<div class="mb-[20px]">
    @if($label)
        <label for="{{ $fieldId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if($required) required @endif
        {{ $attributes->merge([
            'class' => 'block w-full border rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-[15px] py-[10px]'
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Если переданы options – рендерим их --}}
        @if(!empty($options))
            @foreach($options as $key => $item)
                @php
                    // Объект (модель, stdClass и т.п.)
                    if (is_object($item)) {
                        $value = $optionValue ? ($item->{$optionValue} ?? null) : ($item->id ?? $key);
                        $text  = $optionLabel ? ($item->{$optionLabel} ?? null) : ($item->name ?? $value);
                    }
                    // Массив
                    elseif (is_array($item)) {
                        $value = $optionValue
                            ? ($item[$optionValue] ?? null)
                            : ($item['value'] ?? $key);

                        $text  = $optionLabel
                            ? ($item[$optionLabel] ?? null)
                            : ($item['label'] ?? $item['name'] ?? $value);
                    }
                    // Скаляры (например ['en' => 'English'])
                    else {
                        $value = is_int($key) ? $item : $key;
                        $text  = $item;
                    }
                @endphp

                <option value="{{ $value }}" @selected((string)$current === (string)$value)>
                    {{ $text }}
                </option>
            @endforeach
        @else
            {{-- Если options не переданы, можно самому написать <option> через слот --}}
            {{ $slot }}
        @endif
    </select>

    @error($name)
    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

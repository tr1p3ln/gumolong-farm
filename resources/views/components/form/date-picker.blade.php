@props([
    'name',
    'value'  => null,
    'label'  => null,
])

@php
    $resolvedValue = old($name, $value ?? now()->toDateString());
@endphp

<div class="flex flex-col gap-1">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input
        type="date"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $resolvedValue }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-md border-gray-300 shadow-sm text-sm text-gray-900
                        focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-30
                        transition duration-150 ease-in-out',
        ]) }}
    >

    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

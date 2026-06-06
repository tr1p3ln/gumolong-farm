@props([
    'name',
    'gender',
    'value' => '',
])

@php
    $prefix = $gender === 'jantan' ? 'J-' : 'B-';
    $prefixColor = $gender === 'jantan' ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-pink-700 bg-pink-50 border-pink-200';
@endphp

<div class="flex rounded-md shadow-sm">
    {{-- Prefix badge --}}
    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 text-sm font-semibold select-none {{ $prefixColor }}">
        {{ $prefix }}
    </span>

    {{-- Numeric input --}}
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        inputmode="numeric"
        pattern="[0-9]*"
        placeholder="0001"
        {{ $attributes->merge([
            'class' => 'flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300
                        text-sm text-gray-900 placeholder-gray-400
                        focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                        transition duration-150 ease-in-out',
        ]) }}
    >
</div>

{{-- Ear tag preview --}}
<p class="mt-1 text-xs text-gray-400">
    Nomor ear tag: <span class="font-mono font-medium text-gray-600">{{ $prefix }}<span id="{{ $name }}_preview">{{ $value ?: '___' }}</span></span>
</p>

<script>
    (function () {
        const input   = document.getElementById('{{ $name }}');
        const preview = document.getElementById('{{ $name }}_preview');
        if (!input || !preview) return;
        input.addEventListener('input', function () {
            preview.textContent = this.value || '___';
        });
    })();
</script>

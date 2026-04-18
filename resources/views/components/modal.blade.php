{{--
    Usage:
    <div x-data="{ open: false }">
        <button @click="open = true">Open</button>
        <x-modal>
            <x-slot name="title">Judul Modal</x-slot>
            Isi konten modal di sini.
            <x-slot name="footer">
                <button @click="open = false">Tutup</button>
            </x-slot>
        </x-modal>
    </div>
--}}

{{-- Backdrop + modal, controlled by parent x-data "open" --}}
<div
    x-show="open"
    x-cloak
    x-trap.noscroll="open"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="absolute inset-0 bg-black bg-opacity-50"
        aria-hidden="true"
    ></div>

    {{-- Modal box --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative z-10 w-full max-w-md bg-white rounded-lg shadow-xl overflow-hidden"
        @click.stop
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">
                {{ $title ?? '' }}
            </h3>
            <button
                @click="open = false"
                type="button"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-150 rounded-md p-1 -mr-1 focus:outline-none focus:ring-2 focus:ring-primary"
                aria-label="Tutup"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-4 text-sm text-gray-700">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if (isset($footer) && $footer->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

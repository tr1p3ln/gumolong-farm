<div class="bg-white rounded-lg shadow-sm overflow-hidden">

    {{-- Optional search bar slot --}}
    @if (isset($search) && $search->isNotEmpty())
        <div class="px-4 py-3 border-b border-gray-200">
            {{ $search }}
        </div>
    @endif

    {{-- Table wrapper --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">

            {{-- Column headers slot --}}
            <thead class="bg-gray-50">
                <tr>
                    {{ $headers }}
                </tr>
            </thead>

            {{-- Rows slot --}}
            <tbody class="bg-white divide-y divide-gray-100">
                {{ $body }}
            </tbody>

        </table>
    </div>

    {{-- Optional pagination slot --}}
    @if (isset($pagination) && $pagination->isNotEmpty())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $pagination }}
        </div>
    @endif

</div>

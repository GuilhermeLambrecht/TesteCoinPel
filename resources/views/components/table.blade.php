@props([
    'headers' => [],
])

<div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        @if (count($headers))
            <thead class="bg-gray-50">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-gray-100 text-brand-900">
            {{ $slot }}
        </tbody>
    </table>
</div>

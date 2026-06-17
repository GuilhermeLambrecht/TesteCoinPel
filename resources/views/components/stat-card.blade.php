@props([
    'label',
    'value',
])

<div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6">
    <div>
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        <p class="mt-2 text-3xl font-semibold text-brand-700">{{ $value }}</p>
    </div>

    @isset($icon)
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                {{ $icon }}
            </svg>
        </span>
    @endisset
</div>

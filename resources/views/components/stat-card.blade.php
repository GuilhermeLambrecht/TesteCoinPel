@props([
    'label',
    'value',
])

<div class="flex items-center justify-between rounded-xl border border-brand-100 bg-white p-5 shadow-sm">
    <div>
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        <p class="mt-1 text-3xl font-bold text-brand-700">{{ $value }}</p>
    </div>

    @isset($icon)
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                {{ $icon }}
            </svg>
        </span>
    @endisset
</div>

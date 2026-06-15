@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-brand-900">
            {{ $label }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900 '
                .'placeholder:text-gray-400 transition focus:border-brand-500 focus:outline-none '
                .'focus:ring-2 focus:ring-brand-500/30',
        ]) }}
    />

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @else
        @if ($hint)
            <p class="text-sm text-gray-500">{{ $hint }}</p>
        @endif
    @enderror
</div>

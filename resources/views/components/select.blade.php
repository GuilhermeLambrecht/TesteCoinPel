@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-brand-900">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-brand-200 bg-white px-3.5 py-2.5 text-sm text-brand-900 '
                .'transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30',
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>
                {{ $text }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

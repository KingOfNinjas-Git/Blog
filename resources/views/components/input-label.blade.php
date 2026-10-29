@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1.5 block text-sm font-bold text-ink']) }}>
    {{ $value ?? $slot }}
</label>

@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl bg-brand-soft px-4 py-3 text-sm font-semibold text-brand', 'role' => 'status']) }}>
        {{ $status }}
    </div>
@endif

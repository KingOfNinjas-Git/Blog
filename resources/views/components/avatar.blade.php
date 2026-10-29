@props(['name', 'size' => 'sm'])
{{-- A round badge with the person's initial; the colour is always the same for the same name. --}}
@php
    $colors = ['#2446e0', '#0a7f96', '#7a3fd1', '#b4390f', '#1a7f4b', '#a1246d', '#4b5563'];
    $color = $colors[crc32((string) $name) % count($colors)];
@endphp
<span {{ $attributes->class(['avatar', 'avatar-lg' => $size === 'lg']) }} style="--avatar: {{ $color }}" aria-hidden="true">{{ mb_strtoupper(mb_substr((string) $name, 0, 1)) }}</span>

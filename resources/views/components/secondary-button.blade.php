<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-ghost disabled:opacity-40']) }}>
    {{ $slot }}
</button>

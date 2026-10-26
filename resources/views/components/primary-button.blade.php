<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#048BA6] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0491AA] focus:bg-[#0491AA] active:bg-[#04809D] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

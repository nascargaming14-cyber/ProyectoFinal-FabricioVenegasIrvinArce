<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brick-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brick-red-dark focus:bg-brick-red-dark active:bg-brick-red-dark focus:outline-none focus:ring-2 focus:ring-brick-red focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-rose-500 border-2 border-rose-200 rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-rose-400 focus:outline-none focus:border-rose-200 focus:shadow-outline-red active:bg-rose-600 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

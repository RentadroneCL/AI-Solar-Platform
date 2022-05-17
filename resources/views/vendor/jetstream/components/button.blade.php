<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-blue-500 border-2 border-blue-200 rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-blue-400 active:bg-blue-600 focus:outline-none focus:border-blue-600 focus:shadow-outline-blue disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

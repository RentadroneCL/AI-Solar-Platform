<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-slate-50 border-2 border-slate-200 rounded-lg font-semibold text-sm text-slate-900 tracking-widest shadow-sm hover:text-gray-800 focus:outline-none focus:border-slate-200 focus:shadow-outline-blue active:text-slate-800 active:bg-slate-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

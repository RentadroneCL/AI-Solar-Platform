<a {{ $attributes->merge(['href' => '#', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-blue-500 dark:bg-indigo-500 border-2 border-blue-200 dark:border-indigo-400 rounded-lg font-semibold text-sm text-white tracking-widest hover:bg-blue-400 dark:hover:bg-indigo-400 active:bg-blue-600 dark:active:bg-indigo-600 focus:outline-none focus:border-blue-600 dark:focus:border-indigo-600 focus:shadow-outline-blue disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>

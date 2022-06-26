@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block pl-3 pr-4 py-2 border-l-4 border-blue-400 text-base font-medium text-blue-700 dark:text-slate-400 bg-blue-50 dark:bg-slate-600 focus:outline-none focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700 dark:focus:text-white transition duration-150 ease-in-out'
            : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-slate-400 dark:hover:text-white hover:text-gray-800 hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 focus:outline-none dark:focus:text-white focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 dark:focus:border-blue-400 dark:focus:bg-slate-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

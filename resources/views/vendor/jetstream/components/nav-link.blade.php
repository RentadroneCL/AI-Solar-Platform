@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-blue-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-blue-700 transition duration-150 ease-in-out dark:text-white'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 dark:text-slate-400 text-gray-500 hover:text-gray-700 dark:hover:text-white hover:border-slate-500 dark:hover:border-slate-600 focus:outline-none dark:focus:text-white focus:text-gray-700 dark:focus:border-blue-400 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

<div class="md:grid md:grid-cols-3 md:gap-6" {{ $attributes }}>
    <x-jet-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-jet-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <div class="px-4 py-5 bg-white shadow sm:p-6 sm:rounded-lg dark:bg-slate-800 ring-1 ring-slate-900/5 dark:text-slate-400 dark:border-slate-600 dark:shadow-sm">
            {{ $content }}
        </div>
    </div>
</div>

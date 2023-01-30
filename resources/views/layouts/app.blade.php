<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'simplemap.io') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @livewireStyles

    <!-- Scripts -->
    <script defer src="{{ mix('js/app.js') }}"></script>
  </head>
  <body class="font-sans antialiased bg-gray-100 text-slate-500 dark:bg-slate-900 dark:text-slate-400">
    <x-jet-banner />

    <div class="min-h-screen">
      @livewire('navigation-menu')

      <!-- Page Heading -->
      @if (isset($header))
        <header class="pt-16 bg-white shadow dark:bg-slate-800/25 dark:text-slate-400">
          <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{ $header }}
          </div>
        </header>
      @endif

      <!-- Page Content -->
      <main class="h-full">
        <x-alert />
        @if (session('status'))
          <div class="p-4 mb-4 text-sm font-medium text-blue-600 bg-blue-100 border-2 border-blue-300 shadow-sm">
            {{ session('status') }}
          </div>
        @endif

        {{ $slot }}
      </main>
    </div>

    <x-application-footer></x-application-footer>

    @livewireScripts

    @stack('modals')

    @stack('scripts')
  </body>
</html>

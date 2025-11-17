<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Theme colors for browser UI chrome, aligned with Tailwind theme tokens -->
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="#F9FAFB"> {{-- background.DEFAULT --}}
        <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#020617"> {{-- background.inverse --}}

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <!-- Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Theme bootstrap: applies dark or light mode before paint -->
        <script>
            (function applyInitialTheme() {
                var root = document.documentElement;
                if (!root) {
                    return;
                }

                var storedTheme = window.localStorage.getItem('theme');
                var systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                var useDark;

                if (storedTheme === 'dark') {
                    useDark = true;
                } else if (storedTheme === 'light') {
                    useDark = false;
                } else {
                    useDark = systemPrefersDark;
                }

                if (useDark) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            })();

            window.toggleTheme = function toggleTheme() {
                var root = document.documentElement;
                if (!root) {
                    return;
                }

                var isDark = root.classList.toggle('dark');
                window.localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };
        </script>
    </head>

    <body class="font-sans antialiased bg-background text-text dark:bg-background-inverse dark:text-text-inverse">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <div class="flex-1 lg:pl-64 flex flex-col">
                <div class="flex-1 flex flex-col">
                    @isset($header)
                        <header class="pt-6 sm:pt-8 lg:pt-12 pb-4 bg-surface-base dark:bg-surface-inverse-raised shadow-card-soft sm:mb-4 lg:mb-6">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 border-b border-border-subtle dark:border-border-inverse">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="flex-1 mt-6">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </main>

                    <footer class="mt-6">
                        <div class="flex flex-col items-center justify-center sm:flex-row sm:items-baseline sm:gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 ">
                            <span class="text-sm font-normal text-text-muted">Developed under the MIT License by </span>
                            <span class="font-bold">Felipe Ruiz Terrazas</span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>

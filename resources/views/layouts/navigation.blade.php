{{-- resources/views/layouts/navigation.blade.php --}}

@php
    $navItems = [
        [
            'label'  => __('Dashboard'),
            'route'  => 'dashboard',
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label'  => __('Students'),
            'route'  => 'students.index',
            'active' => request()->routeIs('students.*'),
        ],
        [
            'label'  => __('Teachers'),
            'route'  => 'teachers.index',
            'active' => request()->routeIs('teachers.*'),
        ],
        [
            'label'  => __('Subjects'),
            'route'  => 'subjects.index',
            'active' => request()->routeIs('subjects.*'),
        ],
        [
            'label'  => __('Class groups'),
            'route'  => 'class-groups.index',
            'active' => request()->routeIs('class-groups.*'),
        ],
        [
            'label'  => __('Mentorships'),
            'route'  => 'mentorships.index',
            'active' => request()->routeIs('mentorships.*'),
        ],
    ];
@endphp

<div x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">
    {{-- MOBILE NAVIGATION (ONLY < sm, Jetstream-like dropdown) --}}
    <nav
        x-data="{ open: false }"
        class="sm:hidden bg-surface-base dark:bg-surface-inverse-raised border-b border-border-subtle dark:border-border-inverse"
    >
        <!-- Primary Navigation Menu (mobile only) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="block h-9 w-auto fill-current text-text dark:text-text-inverse" />
                            <span class="text-sm font-semibold text-text dark:text-text-inverse">
                                {{ config('app.name', 'Application') }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Mobile hamburger for dropdown -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-text-subtle dark:text-text-inverse-subtle
                               hover:text-text dark:hover:text-text-inverse
                               hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle
                               focus:outline-none focus:bg-surface-subtle dark:focus:bg-surface-inverse-subtle
                               focus:text-text dark:focus:text-text-inverse transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">Toggle navigation</span>
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                :class="{ 'hidden': open, 'inline-flex': !open }"
                                class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ 'hidden': !open, 'inline-flex': open }"
                                class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu (dropdown below header) -->
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden w-full">
            <div class="pt-2 pb-3 space-y-1 border-t border-border-subtle dark:border-border-inverse">
                @foreach ($navItems as $item)
                    <x-responsive-nav-link
                        :href="route($item['route'])"
                        :active="$item['active']"
                    >
                        {{ $item['label'] }}
                    </x-responsive-nav-link>
                @endforeach
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-border-subtle dark:border-border-inverse">
                <div class="px-4 max-w-[80%]">
                    <div class="font-medium text-base text-text dark:text-text-inverse truncate">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="font-medium text-sm text-text-muted dark:text-text-inverse-muted truncate">
                        {{ Auth::user()->email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link
                            :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- HEADER (SM–LG) --}}
    <header class="hidden sm:block lg:hidden bg-surface-base dark:bg-surface-inverse-raised border-b border-border-subtle dark:border-border-inverse">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-8 w-auto fill-current text-text dark:text-text-inverse" />
                        <span class="text-sm font-semibold tracking-tight text-text dark:text-text-inverse">
                            {{ config('app.name', 'Application') }}
                        </span>
                    </a>
                </div>

                <div class="flex items-center">
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-text-subtle dark:text-text-inverse-subtle
                               hover:text-text dark:hover:text-text-inverse
                               hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle
                               focus:outline-none focus:ring-2 focus:ring-offset-2
                               focus:ring-primary-500
                               focus:ring-offset-background dark:focus:ring-offset-background-inverse
                               transition duration-150 ease-in-out"
                    >
                        <span class="sr-only">Open navigation</span>
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- BACKDROP SIDEBAR (SM–LG) --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        class="hidden sm:block lg:hidden fixed inset-0 z-30 bg-overlay-soft"
        @click="sidebarOpen = false"
    ></div>

    {{-- SIDEBAR (SM–LG: toggle; ≥ LG: fixed) --}}
    <aside
        x-cloak
        @click.stop
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="hidden sm:flex fixed inset-y-0 left-0 z-40 w-64 transform transition-transform duration-200 ease-in-out
               bg-surface-base dark:bg-surface-inverse-raised border-r border-border-subtle dark:border-border-inverse
               -translate-x-full lg:translate-x-0 h-screen shadow-lg lg:shadow-md"
    >
        <div class="flex flex-col flex-1 h-full">
            {{-- Sidebar header with logo --}}
            <div class="flex items-center h-16 px-5 border-b border-border-subtle dark:border-border-inverse">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-text dark:text-text-inverse" />
                    <span class="text-sm font-semibold tracking-tight text-text dark:text-text-inverse">
                        {{ config('app.name', 'Application') }}
                    </span>
                </a>

                {{-- Close button (sm–lg only) --}}
                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="ml-auto inline-flex items-center justify-center p-1.5 rounded-md
                           text-text-subtle dark:text-text-inverse-subtle
                           hover:text-text dark:hover:text-text-inverse
                           hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle
                           focus:outline-none focus:ring-2 focus:ring-offset-2
                           focus:ring-primary-500
                           focus:ring-offset-background dark:focus:ring-offset-background-inverse lg:hidden"
                >
                    <span class="sr-only">Close navigation</span>
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation items --}}
            <div class="flex-1 flex flex-col overflow-y-auto">
                <nav class="px-3 py-4 text-sm">
                    <h2 class="px-2 mb-3 text-[0.70rem] font-semibold uppercase tracking-wide text-text-subtle/70 dark:text-text-inverse-subtle/80">
                        {{ __('Navigation') }}
                    </h2>

                    <div class="space-y-1">
                        @foreach ($navItems as $item)
                            @php
                                $isActive = $item['active'];
                                $classes = 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium w-full text-left transition-colors duration-150 ';
                                $classes .= $isActive
                                    ? 'bg-primary-50 text-primary-900 shadow-sm dark:bg-primary-900/40 dark:text-primary-50'
                                    : 'text-text-muted dark:text-text-inverse-muted hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle hover:text-text dark:hover:text-text-inverse';
                            @endphp

                            <x-nav-link
                                :href="route($item['route'])"
                                :active="$isActive"
                                class="{{ $classes }}"
                            >
                                <span class="truncate">{{ $item['label'] }}</span>
                            </x-nav-link>
                        @endforeach
                    </div>
                </nav>
            </div>

            {{-- User info and actions --}}
            <div class="border-t border-border-subtle dark:border-border-inverse px-4 py-4 text-sm bg-surface-subtle/60 dark:bg-surface-inverse-subtle/60">
                <div class="flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-text dark:text-text-inverse truncate">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-text-muted dark:text-text-inverse-muted truncate">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-1">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="block rounded-md px-3 py-2 text-text-muted dark:text-text-inverse-muted
                               hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle
                               hover:text-text dark:hover:text-text-inverse text-sm"
                    >
                        {{ __('Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full text-left rounded-md px-3 py-2 text-text-muted dark:text-text-inverse-muted
                                   hover:bg-surface-subtle dark:hover:bg-surface-inverse-subtle
                                   hover:text-text dark:hover:text-text-inverse text-sm"
                        >
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</div>

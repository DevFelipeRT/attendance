@props(['active' => false])

@php
    $baseClasses = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                    focus:outline-none transition duration-150 ease-in-out';

    $classes = ($active ?? false)
        ? $baseClasses . ' border-primary-500 text-text dark:text-text-inverse focus:border-primary-600'
        : $baseClasses . ' border-transparent
                          text-text-muted dark:text-text-inverse-muted
                          hover:text-text dark:hover:text-text-inverse
                          hover:border-border-subtle dark:hover:border-border-inverse
                          focus:text-text dark:focus:text-text-inverse
                          focus:border-border-strong dark:focus:border-border-inverse-strong';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

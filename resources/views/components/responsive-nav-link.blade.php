@props(['active' => false])

@php
    $baseClasses = 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium
                    focus:outline-none transition duration-150 ease-in-out';

    $classes = ($active ?? false)
        ? $baseClasses . ' border-primary-500
                          text-text dark:text-text-inverse
                          bg-background-muted dark:bg-surface-inverse-subtle
                          focus:border-primary-600
                          focus:bg-background-subtle dark:focus:bg-surface-inverse-raised'
        : $baseClasses . ' border-transparent
                          text-text-muted dark:text-text-inverse-muted
                          hover:text-text dark:hover:text-text-inverse
                          hover:bg-background-muted dark:hover:bg-surface-inverse-subtle
                          hover:border-border-subtle dark:hover:border-border-inverse
                          focus:text-text dark:focus:text-text-inverse
                          focus:bg-background-muted dark:focus:bg-surface-inverse-subtle
                          focus:border-border-strong dark:focus:border-border-inverse-strong';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

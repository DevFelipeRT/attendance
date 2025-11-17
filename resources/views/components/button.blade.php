{{-- resources/views/components/button.blade.php --}}
@props([
    'as' => 'button',       // 'button' or 'a'
    'variant' => 'default', // default | primary | secondary | ghost | outline | danger
    'size' => 'md',         // sm | md | lg
    'type' => 'button',     // button | submit | reset (used only when rendering <button>)
    'responsive' => true    // true | false (activates wrapper)
])

@php
    $wrapperClasses = implode(' ', [
        'flex min-w-[64px] w-full items-center justify-center',
        'sm:w-fit sm:items-start sm:justify-start',
    ]);

    $baseClasses = implode(' ', [
        'inline-flex items-center justify-center',
        'w-full max-w-sm place-self-center',
        'rounded-lg font-semibold tracking-wide',
        'focus:outline-none focus:ring-2 focus:ring-offset-2',
        'transition ease-in-out duration-150',
        'disabled:opacity-60 disabled:cursor-not-allowed',
        'whitespace-nowrap',
    ]);

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-5 py-3 text-sm',
        default => 'px-4 py-2 text-xs',
    };

    $variantClasses = match ($variant) {
        'primary' => implode(' ', [
            'bg-action-primary-bg text-text-onPrimary',
            'hover:bg-action-primary-bgHover active:bg-action-primary-bgActive',
            'border border-transparent',
            'shadow-card-soft',
            'focus:ring-action-primary-ring',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),

        'secondary' => implode(' ', [
            'bg-action-secondary-bg text-text-onSecondary',
            'hover:bg-action-secondary-bgHover active:bg-action-secondary-bgActive',
            'border border-transparent',
            'shadow-card-soft',
            'focus:ring-action-secondary-ring',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),

        'ghost' => implode(' ', [
            'bg-action-ghost-bg text-action-ghost-fg',
            'border border-action-ghost-border',
            'hover:bg-action-ghost-bgHover active:bg-action-ghost-bgActive',
            'hover:border-border-strong dark:hover:border-border-inverse-strong',
            'focus:ring-action-ghost-ring',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),

        'outline' => implode(' ', [
            'bg-transparent',
            'text-text dark:text-text-inverse',
            'border border-border-strong dark:border-border-inverse-strong',
            'hover:bg-background-muted dark:hover:bg-surface-inverse-raised',
            'active:bg-background-subtle dark:active:bg-surface-inverse-raised',
            'focus:ring-action-subtle-ring',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),

        'danger' => implode(' ', [
            'bg-status-error-bg text-status-error-fg',
            'border border-status-error-border',
            'hover:bg-status-error-bg active:bg-status-error-bg',
            'focus:ring-status-error-border',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),

        default => implode(' ', [
            'bg-surface-base dark:bg-surface-inverse',
            'text-text dark:text-text-inverse',
            'border border-border-subtle dark:border-border-inverse',
            'shadow-card-soft',
            'hover:bg-background-muted dark:hover:bg-surface-inverse-raised',
            'active:bg-background-subtle dark:active:bg-surface-inverse-raised',
            'focus:ring-action-subtle-ring',
            'focus:ring-offset-background-muted',
            'dark:focus:ring-offset-background-inverse',
        ]),
    };

    $classes = trim($baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses);
    $responsive = filter_var($responsive, FILTER_VALIDATE_BOOL);
@endphp

@if($responsive)
    <div {{ $attributes->merge(['class' => $wrapperClasses]) }}>
        @if ($as === 'a')
            <a {{ $attributes->except('class')->merge(['class' => $classes]) }}>
                {{ $slot }}
            </a>
        @else
            <button {{ $attributes->except('class')->merge(['type' => $type, 'class' => $classes]) }}>
                {{ $slot }}
            </button>
        @endif
    </div>
@else
    <div class="min-w-[64px] w-fit">
        @if ($as === 'a')
            <a {{ $attributes->merge(['class' => $classes]) }}>
                {{ $slot }}
            </a>
        @else
            <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
                {{ $slot }}
            </button>
        @endif
    </div>
@endif

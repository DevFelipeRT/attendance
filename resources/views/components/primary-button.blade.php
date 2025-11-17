<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
            inline-flex items-center px-4 py-2
            rounded-2xl border border-transparent
            bg-action-primary-bg text-text-onPrimary
            font-semibold text-xs uppercase tracking-widest
            shadow-card-soft
            hover:bg-action-primary-bgHover
            active:bg-action-primary-bgActive
            focus:outline-none
            focus:ring-2 focus:ring-action-primary-ring
            focus:ring-offset-2
            focus:ring-offset-background-muted
            dark:focus:ring-offset-background-inverse
            transition ease-in-out duration-150
            disabled:opacity-60 disabled:cursor-not-allowed
        '
    ]) }}
>
    {{ $slot }}
</button>

{{-- resources/views/components/modal.blade.php

Reusable, accessible modal with backward-compatible API.

Existing props:
- string $name
- bool   $show        default false
- string $maxWidth    'sm'|'md'|'lg'|'xl'|'2xl' (default '2xl')

New optional props (defaults preserve current behavior):
- string $position         'top'|'center' (default 'top')
- bool   $closeOnBackdrop  default true
- bool   $closeOnEscape    default true
--}}

@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',

    // New (optional)
    'position' => 'top',
    'closeOnBackdrop' => true,
    'closeOnEscape' => true,
])

@php
    $maxWidthClass = [
        'sm'  => 'sm:max-w-sm',
        'md'  => 'sm:max-w-md',
        'lg'  => 'sm:max-w-lg',
        'xl'  => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth] ?? 'sm:max-w-2xl';

    $containerBase = 'fixed inset-0 z-50';
    $containerLayout = $position === 'center'
        ? 'flex items-center justify-center px-4'
        : 'overflow-y-auto px-4 py-6';

    $panelBase = 'bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all w-full ' . $maxWidthClass . ' sm:mx-auto';
    $panelLayout = $position === 'center'
        ? 'max-h-[calc(100vh-3rem)] overflow-auto'
        : 'mb-6';
@endphp

<div
    x-data="{
        show: @js((bool) $show),
        name: @js($name),
        closeOnBackdrop: @js((bool) $closeOnBackdrop),
        closeOnEscape: @js((bool) $closeOnEscape),

        opener: null,

        focusables() {
            const selector = `a, button, input:not([type='hidden']), textarea, select, details, [tabindex]:not([tabindex='-1'])`;
            return [...$el.querySelectorAll(selector)].filter(el =>
                !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden')
            );
        },
        firstFocusable() { return this.focusables()[0]; },
        lastFocusable()  { const list = this.focusables(); return list[list.length - 1]; },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1; },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },

        lockScroll(shouldLock) {
            const body = document.body;
            if (!body) return;
            if (shouldLock) {
                const sw = window.innerWidth - document.documentElement.clientWidth;
                body.dataset.modalScrollLock = '1';
                body.style.overflow = 'hidden';
                if (sw > 0) body.style.paddingRight = `${sw}px`;
            } else {
                if (body.dataset.modalScrollLock) {
                    delete body.dataset.modalScrollLock;
                    body.style.overflow = '';
                    body.style.paddingRight = '';
                }
            }
        },

        open() {
            if (this.show) return;
            this.opener = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            this.lockScroll(true);
            this.show = true;

            this.$nextTick(() => {
                const panel = this.$refs.panel;
                const target = panel?.querySelector('[data-autofocus]') || this.firstFocusable() || panel;
                target?.focus?.({ preventScroll: true });
            });
        },

        close() {
            if (!this.show) return;
            this.show = false;
            this.lockScroll(false);
            if (this.opener?.focus) this.opener.focus({ preventScroll: true });
            this.opener = null;
        },
    }"
    x-init="$watch('show', value => { if (value) { {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable()?.focus(), 100)' : '' }} } });"
    x-on:open-modal.window="($event.detail === name) ? open() : null"
    x-on:close-modal.window="($event.detail === name) ? close() : null"
    x-on:toggle-modal.window="($event.detail === name) ? (show ? close() : open()) : null"
    x-on:close.stop="close()"
    x-on:keydown.escape.window.prevent.stop="closeOnEscape ? close() : null"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable()?.focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable()?.focus()"
    x-cloak
    x-show="show"
    {{ $attributes->class("$containerBase $containerLayout") }}
    role="dialog"
    aria-modal="true"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        @click="closeOnBackdrop ? close() : null"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        aria-hidden="true"
    >
        <div class="absolute inset-0 bg-gray-900/75 dark:bg-black/70"></div>
    </div>

    <div
        x-show="show"
        x-ref="panel"
        class="{{ $panelBase }} {{ $panelLayout }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>

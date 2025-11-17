{{-- resources/views/components/action-menu.blade.php

Mobile action menu with customizable trigger, backdrop, placement, and item styling.
Accessible (ARIA, Escape-to-close, focus on first item). Supports link and form items.
Automatically flips up when there is not enough space below the trigger.

Props:
- array<int, array<string,mixed>> $items
    Each item:
      type: 'link'|'form' (default 'link')
      label: string
      href?: string
      action?: string
      method?: 'POST'|'PUT'|'PATCH'|'DELETE' (default 'POST')
      confirm?: string
      classes?: string
- string|null  $triggerAriaLabel   Accessible label for the trigger button.
- string|null  $triggerClass       Tailwind classes for the trigger button.
- string|null  $triggerContent     Raw HTML for a custom trigger (icon, label, etc.).
- bool|null    $backdrop           Whether to show a blocking backdrop (default true).
- string|null  $backdropClass      Backdrop classes (default aligned to overlay token).
- string|null  $panelClass         Container classes for the menu panel.
- string|null  $itemClass          Base classes for each item.
- string|null  $width              Panel width utility (e.g., 'w-48').
- string|null  $placement          'right'|'left' (default 'right').
- string|null  $offsetClass        Extra vertical offset utility (optional).
- string|null  $zIndex             Z-index utility for the panel (default 'z-50').
- string|null  $showOn             Visibility utility for wrapper (default 'sm:hidden').
- bool|null    $closeOnSelect      Close when an item is activated (default true).
- int|float|null $flipGuard        Minimum space (px) to keep opening down (default 0).
--}}

@props([
    'items'            => [],
    'triggerAriaLabel' => 'Open actions',
    'triggerClass'     => 'inline-flex items-center justify-center p-2 rounded-full
                            text-text-muted dark:text-text-inverse-muted
                            hover:text-text dark:hover:text-action-primary-fg
                            hover:bg-surface-raised dark:hover:bg-surface-inverse-subtle
                            focus-visible:outline-none
                            focus-visible:ring-2 focus-visible:ring-action-primary-ring
                            focus-visible:ring-offset-2
                            focus-visible:ring-offset-surface-base dark:focus-visible:ring-offset-surface-inverse',
    'triggerContent'   => null,
    'backdrop'         => true,
    'backdropClass'    => 'bg-overlay',
    'panelClass'       => 'rounded-2xl bg-surface-base dark:bg-surface-inverse
                            shadow-card border border-border-subtle dark:border-border-inverse p-2',
    'itemClass'        => 'w-full text-left text-sm px-3 py-2 rounded-xl
                            text-text dark:text-text-inverse
                            hover:bg-surface-alt dark:hover:bg-surface-inverse-alt
                            focus:outline-none
                            focus-visible:ring-2 focus-visible:ring-action-primary-ring',
    'width'            => 'w-44',
    'placement'        => 'right',
    'offsetClass'      => null,
    'zIndex'           => 'z-50',
    'showOn'           => 'sm:hidden',
    'closeOnSelect'    => true,
    'flipGuard'        => 0,
])

@php
    $isLeft      = ($placement === 'left');

    $originTop   = $isLeft ? 'origin-top-left'    : 'origin-top-right';
    $originBottom= $isLeft ? 'origin-bottom-left' : 'origin-bottom-right';

    $extraOffset = $offsetClass ? trim($offsetClass) : '';

    $wrapperCls  = trim("relative {$showOn}");
    $menuId      = uniqid('amenu_', true);
    $btnId       = uniqid('amenu_btn_', true);

    $flipGuardNumeric = is_numeric($flipGuard) ? (float) $flipGuard : 0.0;
@endphp

<div
    x-data="{
        open: false,
        openUp: false,
        flipGuard: {{ $flipGuardNumeric }},
        panelStyles: '',

        close() {
            this.open = false;
        },

        toggle() {
            if (this.open) {
                this.close();
                return;
            }

            this.open = true;

            this.$nextTick(() => {
                this.recalculateDirection();
                this.focusFirst();
            });
        },

        recalculateDirection() {
            const trigger = this.$refs.trigger;
            const panel   = this.$refs.panel;

            if (!trigger || !panel) {
                return;
            }

            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const viewportWidth  = window.innerWidth  || document.documentElement.clientWidth;
            const triggerRect    = trigger.getBoundingClientRect();
            const panelRect      = panel.getBoundingClientRect();

            const panelHeight    = panelRect.height || 0;
            const spaceBelow     = viewportHeight - triggerRect.bottom;
            const spaceAbove     = triggerRect.top;
            const guard          = Number(this.flipGuard) || 0;

            const flipByGuard  = spaceBelow <= guard;
            const flipByHeight = spaceBelow < panelHeight;

            this.openUp = (flipByGuard || flipByHeight) && spaceAbove > spaceBelow;

            const offset = 8;

            let top;
            if (this.openUp) {
                top = triggerRect.top - panelHeight - offset;
            } else {
                top = triggerRect.bottom + offset;
            }

            let left = null;
            let right = null;

            if ({{ $isLeft ? 'true' : 'false' }}) {
                left = triggerRect.left;
            } else {
                right = viewportWidth - triggerRect.right;
            }

            let style = `position: fixed; top: ${Math.max(top, 8)}px;`;

            if (left !== null) {
                style += ` left: ${Math.max(left, 8)}px;`;
            }

            if (right !== null) {
                style += ` right: ${Math.max(right, 8)}px;`;
            }

            this.panelStyles = style;
        },

        focusFirst() {
            const first = this.$refs.firstItem;
            if (first && typeof first.focus === 'function') {
                first.focus();
            }
        },

        panelPositionClasses() {
            const extra = '{{ $extraOffset }}';

            if (this.openUp) {
                return '{{ $originBottom }}' + (extra ? ' ' + extra : '');
            }

            return '{{ $originTop }}' + (extra ? ' ' + extra : '');
        }
    }"
    class="{{ $wrapperCls }}"
    @keydown.escape.window="close()"
    @click.stop
    @resize.window="open && recalculateDirection()"
    @scroll.window.passive="open && recalculateDirection()"
>
    <button
        id="{{ $btnId }}"
        x-ref="trigger"
        type="button"
        x-on:click="toggle()"
        x-bind:aria-expanded="open"
        aria-haspopup="menu"
        aria-controls="{{ $menuId }}"
        aria-label="{{ $triggerAriaLabel }}"
        class="{{ $triggerClass }}"
    >
        @if($triggerContent)
            {!! $triggerContent !!}
        @else
            {{-- Tabler Dots Vertical (inline SVG) --}}
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M12 5v.01" />
                <path d="M12 12v.01" />
                <path d="M12 19v.01" />
            </svg>
        @endif
    </button>

    @if($backdrop)
        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-40 {{ $backdropClass }}"
            aria-hidden="true"
            @click="close()"
        ></div>
    @endif

    <template x-teleport="body">
        <div
            id="{{ $menuId }}"
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-ref="panel"
            :class="panelPositionClasses()"
            x-bind:style="panelStyles"
            class="{{ $zIndex }} {{ $width }} {{ $panelClass }}"
            role="menu"
            aria-labelledby="{{ $btnId }}"
            @click.outside="{{ $backdrop ? '' : 'close()' }}"
        >
            <div class="flex flex-col gap-1" role="none">
                @foreach($items as $index => $i)
                    @php
                        $type       = ($i['type'] ?? 'link');
                        $label      = (string) ($i['label'] ?? 'Action');
                        $classes    = trim(($i['classes'] ?? '') . ' ' . $itemClass);
                        $confirmMsg = $i['confirm'] ?? null;
                        $method     = strtoupper($i['method'] ?? 'POST');
                        $href       = $i['href'] ?? '#';
                        $action     = $i['action'] ?? '#';
                        $firstAttr  = $index === 0 ? 'x-ref=firstItem' : '';
                    @endphp

                    @if($type === 'form')
                        <form
                            action="{{ $action }}"
                            method="POST"
                            role="none"
                            onsubmit="return {{ $confirmMsg !== null ? 'confirm('.json_encode($confirmMsg).')' : 'true' }};"
                            @submit.stop
                        >
                            @csrf
                            @if($method !== 'POST')
                                @method($method)
                            @endif
                            <button
                                type="submit"
                                {!! $firstAttr !!}
                                role="menuitem"
                                tabindex="-1"
                                class="{{ $classes }}"
                                @click="{{ $closeOnSelect ? 'close()' : '' }}"
                            >{{ $label }}</button>
                        </form>
                    @else
                        <a
                            href="{{ $href }}"
                            {!! $firstAttr !!}
                            role="menuitem"
                            tabindex="-1"
                            class="{{ $classes }}"
                            @click="{{ $closeOnSelect ? 'close()' : '' }}"
                        >{{ $label }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </template>
</div>

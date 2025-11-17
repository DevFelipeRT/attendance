{{-- resources/views/components/toast.blade.php
    Single toast notification with reliable progress bar and pause/resume.

    Props:
    - string $type        success|error|info|warning
    - string $message     plain text message
    - int    $timeout     auto-dismiss in ms (default 4000). 0 disables auto-close
    - bool   $dismissible manual close control (default true)

    Notes:
    - Light/Dark theme aware palettes with accessible contrast.
    - No layout shift on hover; progress bar pauses on pointer hover.
--}}

@props([
    'type' => 'info',
    'message' => '',
    'timeout' => 4000,
    'dismissible' => true,
])

@php
    // Light theme container palette (background, text, subtle ring)
    $light = [
        'success' => 'bg-emerald-50 text-emerald-900 ring-1 ring-emerald-200',
        'error'   => 'bg-rose-50 text-rose-900 ring-1 ring-rose-200',
        'info'    => 'bg-slate-50 text-slate-900 ring-1 ring-slate-200',
        'warning' => 'bg-amber-50 text-amber-900 ring-1 ring-amber-200',
    ];

    // Dark theme container palette (saturated background, white text, soft ring)
    $dark = [
        'success' => 'dark:bg-emerald-600 dark:text-white dark:ring-1 dark:ring-emerald-500/30',
        'error'   => 'dark:bg-rose-600 dark:text-white dark:ring-1 dark:ring-rose-500/30',
        'info'    => 'dark:bg-slate-700 dark:text-white dark:ring-1 dark:ring-slate-500/30',
        'warning' => 'dark:bg-amber-500 dark:text-slate-900 dark:ring-1 dark:ring-amber-400/30',
    ];

    // Progress bar palette: stronger tone for light, softer for dark
    $barLight = [
        'success' => 'bg-emerald-500',
        'error'   => 'bg-rose-500',
        'info'    => 'bg-slate-500',
        'warning' => 'bg-amber-500',
    ];
    $barDark = [
        'success' => 'bg-emerald-300',
        'error'   => 'bg-rose-300',
        'info'    => 'bg-slate-300',
        'warning' => 'bg-amber-300',
    ];

    $classes    = ($light[$type] ?? $light['info']) . ' ' . ($dark[$type] ?? $dark['info']);
    $barClasses = ($barLight[$type] ?? $barLight['info']) . ' dark:' . ($barDark[$type] ?? $barDark['info']);
@endphp

<div
    x-data="{
        open: true,
        remaining: {{ (int) $timeout }},
        startedAt: 0,
        cycleTotal: 0,
        timer: null,
        animate() {
            if (this.remaining <= 0) return;

            this.startedAt = Date.now();
            this.cycleTotal = this.remaining;

            const bar = this.$refs.bar;
            bar.style.transition = 'none';
            bar.style.width = '100%';

            requestAnimationFrame(() => {
                bar.style.transition = `width ${this.remaining}ms linear`;
                bar.style.width = '0%';
            });

            this.timer = setTimeout(() => { this.open = false; }, this.remaining);
        },
        pause() {
            if (!this.timer) return;
            clearTimeout(this.timer);
            this.timer = null;

            const elapsed = Date.now() - this.startedAt;
            this.remaining = Math.max(0, this.remaining - elapsed);

            const pct = this.cycleTotal > 0 ? (this.remaining / this.cycleTotal) * 100 : 0;
            const bar = this.$refs.bar;
            bar.style.transition = 'none';
            bar.style.width = `${pct}%`;
        },
        resume() {
            if (this.remaining <= 0 || this.timer) return;
            this.animate();
        },
        init() {
            if (this.remaining > 0) this.$nextTick(() => this.animate());
        }
    }"
    x-show="open"
    x-transition.opacity.duration.200ms
    @mouseenter="pause()"
    @mouseleave="resume()"
    class="pointer-events-auto w-full max-w-sm sm:max-w-md lg:max-w-full shadow-lg rounded-lg {{ $classes }}"
    role="status"
    aria-live="polite"
>
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="flex-1 text-sm leading-5">
                {{ $message }}
            </div>

            @if($dismissible)
                <button
                    type="button"
                    @click="open = false"
                    class="inline-flex shrink-0 rounded-md/5 p-1.5 ring-1 ring-inset ring-slate-900/10 hover:bg-black/5
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900/10 focus:ring-offset-transparent
                           dark:ring-white/10 dark:hover:bg-white/10 dark:focus:ring-white/20"
                    aria-label="Dismiss"
                    title="Dismiss"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd"/>
                    </svg>
                </button>
            @endif
        </div>

        @if((int) $timeout > 0)
            <div class="mt-3 h-1 w-full rounded bg-black/10 dark:bg-white/10">
                <div
                    x-ref="bar"
                    class="h-1 {{ $barClasses }} rounded"
                    style="width: 100%;"
                ></div>
            </div>
        @endif
    </div>
</div>

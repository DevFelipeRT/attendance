<section>
    <header>
        <h2 class="text-lg font-medium text-text dark:text-text-inverse">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('put')

        {{-- Current password --}}
        <div>
            <x-input-label for="current_password" :value="__('Current Password')" />
            <x-text-input
                id="current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        {{-- New password --}}
        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm new password --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-button
                type="submit"
                variant="primary"
                size="sm"
            >
                {{ __('Save') }}
            </x-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-status-success-subtleFg"
                >
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>

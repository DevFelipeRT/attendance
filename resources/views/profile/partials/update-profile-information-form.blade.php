<section>
    <header>
        <h2 class="text-lg font-medium text-text dark:text-text-inverse">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
    >
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="email"
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-sm text-text dark:text-text-inverse space-y-2">
                    <p>
                        {{ __('Your email address is unverified.') }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-button
                            type="submit"
                            form="send-verification"
                            variant="outline"
                            size="xs"
                        >
                            {{ __('Click here to re-send the verification email.') }}
                        </x-button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="text-xs font-medium text-status-success-subtleFg">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-button
                type="submit"
                variant="primary"
                size="sm"
            >
                {{ __('Save') }}
            </x-button>

            @if (session('status') === 'profile-updated')
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

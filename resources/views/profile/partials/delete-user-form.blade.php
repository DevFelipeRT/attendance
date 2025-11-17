<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-text dark:text-text-inverse">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        variant="danger"
        size="sm"
    >
        {{ __('Delete Account') }}
    </x-button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="post"
            action="{{ route('profile.destroy') }}"
            class="p-6 space-y-4"
        >
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-text dark:text-text-inverse">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-button
                    type="button"
                    variant="ghost"
                    size="sm"
                    x-on:click="$dispatch('close')"
                >
                    {{ __('Cancel') }}
                </x-button>

                <x-button
                    type="submit"
                    variant="danger"
                    size="sm"
                    class="ms-3"
                >
                    {{ __('Delete Account') }}
                </x-button>
            </div>
        </form>
    </x-modal>
</section>

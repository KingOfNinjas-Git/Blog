<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-ink">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-ink-soft">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- A plain disclosure instead of a pop-up: nothing to load, works with the keyboard and without JavaScript --}}
    <details class="group rounded-2xl border border-line" @if ($errors->userDeletion->isNotEmpty()) open @endif>
        <summary class="btn btn-danger list-none !rounded-2xl [&::-webkit-details-marker]:hidden group-open:hidden">{{ __('Delete Account') }}</summary>

        <form method="post" action="{{ route('profile.destroy') }}" class="p-5">
            @csrf
            @method('delete')

            <h3 class="text-base font-bold text-ink">
                {{ __('Are you sure you want to delete your account?') }}
            </h3>

            <p class="mt-1 text-sm text-ink-soft">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-5">
                <x-input-label for="delete-password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input id="delete-password" name="password" type="password" class="block w-full sm:w-3/4" placeholder="{{ __('Password') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-5 flex justify-end">
                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </details>
</section>

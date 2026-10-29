<x-app-layout>
    <x-slot name="header">
        <p class="kicker">Your account</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="space-y-6">
        <div class="panel p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="panel p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="panel p-6 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

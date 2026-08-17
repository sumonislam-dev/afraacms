<x-guest-layout>
    <div class="text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mt-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4 mt-3">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                <x-admin.icon name="chevron-right" class="h-3.5 w-3.5 rotate-180" />
                {{ __('Back to login') }}
            </a>

            <x-primary-button>
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

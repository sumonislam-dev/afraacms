@props(['projectSlug'])

@php
    $recaptchaEnabled = app(\App\CMS\Services\ContactService::class)->recaptchaEnabled();
@endphp

<div class="mx-auto max-w-xl">
    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-inset ring-green-600/20">
            <x-icon name="check-circle" class="h-5 w-5 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-800 ring-1 ring-inset ring-red-600/20">
            <p class="font-medium">{{ __('Please fix the following:') }}</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.visitor-book.store', $projectSlug) }}" class="space-y-4">
        @csrf

        {{-- Honeypot: invisible to real visitors, bots tend to fill every field. --}}
        <div class="absolute left-[-9999px]" aria-hidden="true">
            <label for="visitor-book-website">{{ __('Leave this field blank') }}</label>
            <input type="text" id="visitor-book-website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <label for="visitor-book-name" class="block text-sm font-medium text-gray-700">{{ __('Your Name') }}</label>
            <input id="visitor-book-name" name="visitor_name" type="text" value="{{ old('visitor_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="visitor-book-email" class="block text-sm font-medium text-gray-700">{{ __('Email (optional)') }}</label>
            <input id="visitor-book-email" name="visitor_email" type="email" value="{{ old('visitor_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500">
            <p class="mt-1 text-xs text-gray-500">{{ __('Kept private - never shown publicly.') }}</p>
        </div>

        <div>
            <label for="visitor-book-opinion" class="block text-sm font-medium text-gray-700">{{ __('Your Opinion') }}</label>
            <textarea id="visitor-book-opinion" name="opinion" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500">{{ old('opinion') }}</textarea>
        </div>

        @if ($recaptchaEnabled)
            <div class="g-recaptcha" data-sitekey="{{ setting('recaptcha_site_key') }}"></div>
            @once
                @push('scripts')
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                @endpush
            @endonce
        @endif

        <button type="submit" class="rounded-md bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
            {{ __('Submit Opinion') }}
        </button>
    </form>
</div>

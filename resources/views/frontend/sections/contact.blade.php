@php
    $recaptchaEnabled = app(\App\CMS\Services\ContactService::class)->recaptchaEnabled();
@endphp

<section class="bg-gray-50 py-16">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
        @if ($section['heading'])
            <h2 class="text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        @if ($section['subheading'])
            <p class="mt-2 text-gray-600">{{ $section['subheading'] }}</p>
        @endif

        <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-700">
            @if (setting('contact_email'))
                <a href="mailto:{{ setting('contact_email') }}" class="hover:text-indigo-600">{{ setting('contact_email') }}</a>
            @endif

            @if (setting('contact_phone'))
                <a href="tel:{{ setting('contact_phone') }}" class="hover:text-indigo-600">{{ setting('contact_phone') }}</a>
            @endif

            @if (setting('contact_address'))
                <span>{{ setting('contact_address') }}</span>
            @endif
        </div>

        @if (setting('google_map'))
            <div class="mt-8 overflow-hidden rounded-lg">
                <iframe src="{{ setting('google_map') }}" class="h-64 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @endif

        <div class="mx-auto mt-10 max-w-xl text-left">
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-inset ring-green-600/20">
                    <x-icon name="check-circle" class="h-5 w-5 flex-shrink-0" />
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

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                @csrf

                {{-- Honeypot: invisible to real visitors, bots tend to fill every field. --}}
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="website">{{ __('Leave this field blank') }}</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact-name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <input id="contact-name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="contact-email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <input id="contact-email" name="email" type="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="contact-subject" class="block text-sm font-medium text-gray-700">{{ __('Subject') }}</label>
                    <input id="contact-subject" name="subject" type="text" value="{{ old('subject') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="contact-message" class="block text-sm font-medium text-gray-700">{{ __('Message') }}</label>
                    <textarea id="contact-message" name="message" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
                </div>

                @if ($recaptchaEnabled)
                    <div class="g-recaptcha" data-sitekey="{{ setting('recaptcha_site_key') }}"></div>
                    @once
                        @push('scripts')
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        @endpush
                    @endonce
                @endif

                <button type="submit" class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                    {{ __('Send Message') }}
                </button>
            </form>
        </div>
    </div>
</section>

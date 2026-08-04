@php
    $recaptchaEnabled = app(\App\CMS\Services\ContactService::class)->recaptchaEnabled();
@endphp

<section class="relative overflow-hidden bg-ink-900 py-20 sm:py-24">
    <div class="relative mx-auto grid max-w-7xl gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div>
            @if ($section['subheading'])
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-brand-400">{{ $section['subheading'] }}</p>
            @endif

            @if ($section['heading'])
                <h2 class="mb-4 font-display text-3xl font-bold text-white sm:text-4xl">{{ $section['heading'] }}</h2>
            @endif

            <ul class="space-y-3 text-sm text-white/80">
                @if (setting('contact_address'))
                    <li class="flex items-start gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>{{ setting('contact_address') }}</li>
                @endif

                @if (setting('contact_phone'))
                    <li class="flex items-center gap-3"><span class="h-2 w-2 shrink-0 rounded-full bg-brand-500"></span><a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}" class="hover:text-white">{{ setting('contact_phone') }}</a></li>
                @endif

                @if (setting('contact_email'))
                    <li class="flex items-center gap-3"><span class="h-2 w-2 shrink-0 rounded-full bg-brand-500"></span><a href="mailto:{{ setting('contact_email') }}" class="hover:text-white">{{ setting('contact_email') }}</a></li>
                @endif
            </ul>

            @if (setting('google_map'))
                <div class="mt-8 overflow-hidden rounded-lg">
                    <iframe src="{{ setting('google_map') }}" class="h-64 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-2xl">
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

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                @csrf

                {{-- Honeypot: invisible to real visitors, bots tend to fill every field. --}}
                <div class="absolute left-[-9999px]" aria-hidden="true">
                    <label for="website">{{ __('Leave this field blank') }}</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label for="contact-name" class="mb-1 block text-sm font-medium text-ink-900/70">{{ __('Your Name') }}</label>
                    <input id="contact-name" name="name" type="text" value="{{ old('name') }}" required placeholder="{{ __('Full name') }}" class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="contact-email" class="mb-1 block text-sm font-medium text-ink-900/70">{{ __('Your Email') }}</label>
                    <input id="contact-email" name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com" class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="contact-subject" class="mb-1 block text-sm font-medium text-ink-900/70">{{ __('Subject') }}</label>
                    <input id="contact-subject" name="subject" type="text" value="{{ old('subject') }}" class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="contact-message" class="mb-1 block text-sm font-medium text-ink-900/70">{{ __('Message') }}</label>
                    <textarea id="contact-message" name="message" rows="4" required placeholder="{{ __('How would you like to help?') }}" class="w-full rounded-lg border border-black/10 px-4 py-2.5 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500">{{ old('message') }}</textarea>
                </div>

                @if ($recaptchaEnabled)
                    <div class="g-recaptcha" data-sitekey="{{ setting('recaptcha_site_key') }}"></div>
                    @once
                        @push('scripts')
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        @endpush
                    @endonce
                @endif

                <button type="submit" class="w-full rounded-lg bg-brand-500 py-3 font-semibold text-white transition hover:bg-brand-600">
                    {{ __('Send Message') }}
                </button>
            </form>
        </div>
    </div>
</section>

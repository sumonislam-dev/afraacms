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
    </div>
</section>

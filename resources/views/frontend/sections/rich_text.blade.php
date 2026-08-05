<section class="py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        @if ($section['body'])
            <div class="prose prose-neutral mt-4 max-w-none text-gray-700">
                {!! str_starts_with(ltrim($section['body']), '<') ? $section['body'] : nl2br(e($section['body'])) !!}
            </div>
        @endif
    </div>
</section>

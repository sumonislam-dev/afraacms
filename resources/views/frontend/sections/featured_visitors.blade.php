@php
    $visitors = app(\App\CMS\Services\FeaturedVisitorService::class)->all();
@endphp

<x-frontend.featured-visitors
    :heading="$section['heading']"
    :subheading="$section['subheading']"
    :items="$visitors"
/>

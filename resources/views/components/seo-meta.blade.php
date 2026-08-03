@props(['title' => null, 'description' => null, 'image' => null, 'url' => null, 'robots' => null])

@php
    $meta = app(\App\CMS\SEO\SeoService::class)->resolve(compact('title', 'description', 'image', 'url', 'robots'));
@endphp

<title>{{ $meta['title'] }}</title>

@if ($meta['description'])
    <meta name="description" content="{{ $meta['description'] }}">
@endif

<meta name="robots" content="{{ $meta['robots'] }}">

<link rel="canonical" href="{{ $meta['url'] }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $meta['site_name'] }}">
<meta property="og:title" content="{{ $meta['title'] }}">
<meta property="og:url" content="{{ $meta['url'] }}">
@if ($meta['description'])
    <meta property="og:description" content="{{ $meta['description'] }}">
@endif
@if ($meta['image'])
    <meta property="og:image" content="{{ $meta['image'] }}">
@endif

<meta name="twitter:card" content="{{ $meta['image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $meta['title'] }}">
@if ($meta['description'])
    <meta name="twitter:description" content="{{ $meta['description'] }}">
@endif
@if ($meta['image'])
    <meta name="twitter:image" content="{{ $meta['image'] }}">
@endif

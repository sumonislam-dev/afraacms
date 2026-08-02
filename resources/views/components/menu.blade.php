@props(['slug'])

@php
    $menuData = menu($slug);
@endphp

@if ($menuData && ! empty($menuData['tree']))
    <nav {{ $attributes }}>
        @include('components._menu-items', ['items' => $menuData['tree'], 'level' => 0])
    </nav>
@endif

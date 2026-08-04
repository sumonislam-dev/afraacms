@props(['sections' => []])

@foreach ($sections as $section)
    @if ($section['anchor'] ?? null)
        <div id="{{ $section['anchor'] }}">
            @includeIf("frontend.sections.{$section['type']}", ['section' => $section])
        </div>
    @else
        @includeIf("frontend.sections.{$section['type']}", ['section' => $section])
    @endif
@endforeach

@props(['sections' => []])

@foreach ($sections as $section)
    @includeIf("frontend.sections.{$section['type']}", ['section' => $section])
@endforeach

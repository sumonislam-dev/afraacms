<x-mail::message>
# {{ __('New Visitor Book Entry') }}

**{{ __('Project') }}:** {{ $entry->project->title }}
**{{ __('From') }}:** {{ $entry->visitor_name }}{{ $entry->visitor_email ? " ({$entry->visitor_email})" : '' }}

{{ $entry->opinion }}

{{ __('This entry is pending approval and will not be shown publicly until you approve it.') }}

<x-mail::button :url="route('admin.visitor-book.show', $entry)">
{{ __('Review Entry') }}
</x-mail::button>

{{ __('Submitted from the visitor book at :site.', ['site' => setting('site_name', config('app.name'))]) }}
</x-mail::message>

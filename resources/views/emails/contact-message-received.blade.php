<x-mail::message>
# {{ __('New Contact Message') }}

**{{ __('From') }}:** {{ $contactMessage->name }} ({{ $contactMessage->email }})

@if ($contactMessage->subject)
**{{ __('Subject') }}:** {{ $contactMessage->subject }}
@endif

{{ $contactMessage->message }}

<x-mail::button :url="route('admin.contact.show', $contactMessage)">
{{ __('View in Inbox') }}
</x-mail::button>

{{ __('Sent from the contact form at :site.', ['site' => setting('site_name', config('app.name'))]) }}
</x-mail::message>

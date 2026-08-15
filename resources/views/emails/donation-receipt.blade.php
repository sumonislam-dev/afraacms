<x-mail::message>
# {{ __('Thank You, :name!', ['name' => $donation->donor_name]) }}

{{ __('We gratefully acknowledge your generous donation to :site.', ['site' => setting('site_name', config('app.name'))]) }}

**{{ __('Receipt Number') }}:** {{ $donation->receipt_number }}
**{{ __('Date') }}:** {{ $donation->donated_at->format('F j, Y') }}
**{{ __('Amount') }}:** {{ $donation->currency }} {{ number_format($donation->amount, 2) }}
**{{ __('Payment Method') }}:** {{ config("donations.methods.{$donation->method}", $donation->method) }}
@if ($donation->project)
**{{ __('Project') }}:** {{ $donation->project->title }}
@endif

{{ __('Please keep this email as your official receipt for this donation.') }}

@if (setting('contact_email') || setting('contact_phone'))
{{ __('If you have any questions, feel free to reach us at :contact.', ['contact' => setting('contact_email', setting('contact_phone')) ]) }}
@endif

{{ __('With appreciation,') }}<br>
{{ setting('site_name', config('app.name')) }}
</x-mail::message>

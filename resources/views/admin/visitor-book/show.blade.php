<x-admin-layout :breadcrumbs="[['label' => __('Visitor Book'), 'url' => route('admin.visitor-book.index')], ['label' => $entry->visitor_name]]">
    <x-slot name="title">{{ __('Visitor Book Entry') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Visitor Book Entry') }}</h2>
    </x-slot>

    <x-admin.card>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Visitor') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $entry->visitor_name }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $entry->visitor_email ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Project') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $entry->project?->title ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Submitted') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $entry->created_at->format('M j, Y g:i A') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('IP Address') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $entry->ip_address ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if ($entry->status === 'approved')
                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">{{ __('Approved') }}</span>
                    @elseif ($entry->status === 'rejected')
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ __('Rejected') }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">{{ __('Pending') }}</span>
                    @endif
                </dd>
            </div>
        </dl>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Opinion') }}</dt>
            <dd class="mt-2 whitespace-pre-line text-sm text-gray-900">{{ $entry->opinion }}</dd>
        </div>
    </x-admin.card>

    <div class="mt-6 flex items-center justify-between">
        <a href="{{ route('admin.visitor-book.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; {{ __('Back to Visitor Book') }}</a>

        <div class="flex gap-3">
            @can('update', $entry)
                @if ($entry->status !== 'approved')
                    <form method="POST" action="{{ route('admin.visitor-book.approve', $entry) }}">
                        @csrf
                        <x-primary-button type="submit">{{ __('Approve') }}</x-primary-button>
                    </form>
                @endif

                @if ($entry->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.visitor-book.reject', $entry) }}">
                        @csrf
                        <x-secondary-button type="submit">{{ __('Reject') }}</x-secondary-button>
                    </form>
                @endif
            @endcan
        </div>
    </div>
</x-admin-layout>

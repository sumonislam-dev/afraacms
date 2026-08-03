<x-admin-layout :breadcrumbs="[['label' => __('Activity Log')]]">
    <x-slot name="title">{{ __('Activity Log') }}</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Activity Log') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('A record of who created, changed, or deleted content across the admin panel.') }}</p>
        </div>
    </x-slot>

    <x-admin.search-form :placeholder="__('Search activity...')" />

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th>{{ __('When') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('User') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Action') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Item') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Details') }}</x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($activities as $activity)
                @php
                    $subject = $activity->subject;
                    $subjectLabel = $subject?->title ?? $subject?->name ?? $subject?->caption ?? $subject?->label ?? null;

                    $parentContext = match (true) {
                        $subject instanceof \App\Models\GalleryItem => $subject->gallery?->title,
                        $subject instanceof \App\Models\MenuItem => $subject->menu?->name,
                        $subject instanceof \App\Models\SectionItem => $subject->section?->heading ?: $subject->section?->type,
                        default => null,
                    };

                    if ($parentContext) {
                        $subjectLabel = trim(($subjectLabel ? "{$subjectLabel} " : '')."(in {$parentContext})");
                    }

                    $changedFields = collect($activity->changes()['attributes'] ?? [])->keys()->all();
                @endphp
                <tr>
                    <x-admin.table-td class="whitespace-nowrap text-sm text-gray-500">
                        {{ $activity->created_at->format('M j, Y g:i A') }}
                    </x-admin.table-td>
                    <x-admin.table-td class="font-medium text-gray-900">
                        {{ $activity->causer?->name ?? __('System') }}
                    </x-admin.table-td>
                    <x-admin.table-td>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                            {{ ucfirst($activity->description) }}
                        </span>
                    </x-admin.table-td>
                    <x-admin.table-td>
                        {{ class_basename($activity->subject_type) }}
                        @if ($subjectLabel)
                            <span class="text-gray-500">&mdash; {{ $subjectLabel }}</span>
                        @endif
                    </x-admin.table-td>
                    <x-admin.table-td class="text-sm text-gray-500">
                        @if (! empty($changedFields))
                            {{ __('Changed:') }} {{ implode(', ', $changedFields) }}
                        @else
                            &mdash;
                        @endif
                    </x-admin.table-td>
                </tr>
            @empty
                <tr>
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No activity recorded yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$activities" />
</x-admin-layout>

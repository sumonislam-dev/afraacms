@php
    $all = collect(app(\App\CMS\Services\TeamService::class)->all());
    $categoryIds = $section['team_category_ids'] ?? [];
    $memberIds = $section['team_member_ids'] ?? [];

    $members = match (true) {
        ! empty($memberIds) => $all->whereIn('id', $memberIds)->values(),
        ! empty($categoryIds) => $all->whereIn('category_id', $categoryIds)->values(),
        default => $all->values(),
    };
@endphp

<x-frontend.team
    :heading="$section['heading']"
    :subheading="$section['subheading']"
    :items="$members->map(fn ($member) => [
        'title' => $member['name'],
        'subtitle' => $member['role'],
        'meta' => implode(' · ', array_filter([$member['country'] ?? null, $member['service_period'] ?? null])) ?: null,
        'body' => $member['bio'],
        'image_url' => $member['photo_url'],
        'url' => $member['link'],
    ])->all()"
/>

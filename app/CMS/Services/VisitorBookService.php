<?php

namespace App\CMS\Services;

use App\Mail\VisitorBookEntrySubmitted;
use App\Models\Project;
use App\Models\VisitorBookEntry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Deliberately queries the database directly rather than using the
 * CachesForFrontend/rememberForever pattern other content services use: a
 * newly approved entry must appear immediately, not after a cache TTL.
 */
class VisitorBookService
{
    /**
     * Record a new visitor book entry as pending and notify the site admin
     * that it needs review (best-effort - a broken/unconfigured mailer must
     * never prevent the entry itself from being recorded).
     */
    public function submit(Project $project, array $data, ?string $ip): VisitorBookEntry
    {
        $entry = $project->visitorBookEntries()->create([
            ...$data,
            'status' => 'pending',
            'ip_address' => $ip,
        ]);

        if ($to = setting('contact_email')) {
            try {
                Mail::to($to)->send(new VisitorBookEntrySubmitted($entry));
            } catch (\Throwable $e) {
                Log::warning('Failed to send visitor book notification email.', ['exception' => $e]);
            }
        }

        return $entry;
    }

    /**
     * Get the approved entries for a single project (looked up by slug,
     * since the public ProjectController works off a cached array that
     * doesn't carry the project's numeric id), newest first.
     *
     * @return array<int, VisitorBookEntry>
     */
    public function approvedForProjectSlug(string $slug, int $limit = 10): array
    {
        return VisitorBookEntry::approved()
            ->whereHas('project', fn ($query) => $query->where('slug', $slug))
            ->latest()
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * Get every approved entry across all projects, newest first, paginated.
     */
    public function allApproved(int $perPage = 15): LengthAwarePaginator
    {
        return VisitorBookEntry::approved()
            ->with('project:id,title,slug')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function approve(VisitorBookEntry $entry): void
    {
        $entry->approve();
    }

    public function reject(VisitorBookEntry $entry): void
    {
        $entry->reject();
    }

    public function delete(VisitorBookEntry $entry): void
    {
        $entry->delete();
    }
}

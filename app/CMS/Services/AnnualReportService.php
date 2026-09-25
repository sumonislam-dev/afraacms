<?php

namespace App\CMS\Services;

use App\CMS\Services\Concerns\CachesForFrontend;
use App\Models\AnnualReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class AnnualReportService
{
    use CachesForFrontend;

    protected function cacheKey(): string
    {
        return 'annual_reports.active';
    }

    /**
     * Every active report, newest year first - for the public listing page.
     *
     * Returns plain arrays, not AnnualReport models: the cache store's
     * serializable_classes hardening (see config/cache.php) strips objects
     * down to __PHP_Incomplete_Class on read, so only arrays/scalars may be
     * cached here (see NewsService/GalleryService for the same pattern).
     *
     * @return array<int, array{id: int, title: string, year: string, attachment_url: ?string}>
     */
    public function all(): array
    {
        return $this->rememberForever(fn () => AnnualReport::active()
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (AnnualReport $report) => [
                'id' => $report->id,
                'title' => $report->title,
                'year' => $report->year,
                'attachment_url' => $report->attachment_url,
            ])
            ->all());
    }

    /**
     * Create a new report.
     */
    public function create(array $data): AnnualReport
    {
        $attachment = Arr::pull($data, 'attachment');

        $report = AnnualReport::create($data);

        if ($attachment instanceof UploadedFile) {
            $report->addMedia($attachment)->toMediaCollection('attachment');
        }

        $this->forget();

        return $report;
    }

    /**
     * Update an existing report.
     */
    public function update(AnnualReport $report, array $data): AnnualReport
    {
        $attachment = Arr::pull($data, 'attachment');
        $removeAttachment = Arr::pull($data, 'remove_attachment', false);

        $report->update($data);

        if ($attachment instanceof UploadedFile) {
            // singleFile() collection: adding a new one replaces the old.
            $report->addMedia($attachment)->toMediaCollection('attachment');
        } elseif ($removeAttachment) {
            $report->clearMediaCollection('attachment');
        }

        $this->forget();

        return $report;
    }

    /**
     * Delete a report.
     */
    public function delete(AnnualReport $report): void
    {
        $report->delete();

        $this->forget();
    }
}

<?php

namespace App\CMS\Imports\Concerns;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * A date cell arrives here as either an Excel serial number (when the
 * source .xlsx column is formatted as a date) or a plain string (typed
 * text, or any CSV upload) - normalize both into a "Y-m-d" string before
 * validation/model construction so the rest of the importer only ever
 * deals with one shape.
 */
trait NormalizesExcelDates
{
    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            // Left as-is: an unparseable string fails the "date" validation
            // rule with a clear message, rather than being silently dropped.
            return (string) $value;
        }
    }
}

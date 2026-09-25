<?php

namespace App\Policies;

use App\Models\AnnualReport;
use App\Models\User;

class AnnualReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('annual_reports.view');
    }

    public function view(User $user, AnnualReport $annualReport): bool
    {
        return $user->can('annual_reports.view');
    }

    public function create(User $user): bool
    {
        return $user->can('annual_reports.create');
    }

    public function update(User $user, AnnualReport $annualReport): bool
    {
        return $user->can('annual_reports.edit');
    }

    public function delete(User $user, AnnualReport $annualReport): bool
    {
        return $user->can('annual_reports.delete');
    }
}

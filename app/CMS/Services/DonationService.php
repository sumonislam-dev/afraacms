<?php

namespace App\CMS\Services;

use App\Mail\DonationReceipt;
use App\Models\Donation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DonationService
{
    /**
     * Record a new donation and, if a donor email was given and it's
     * completed (not a refund entry), send the receipt.
     */
    public function create(array $data): Donation
    {
        $donation = Donation::create($data);

        $this->sendReceipt($donation);

        return $donation;
    }

    /**
     * Update an existing donation.
     */
    public function update(Donation $donation, array $data): Donation
    {
        $donation->update($data);

        return $donation;
    }

    /**
     * Delete a donation.
     */
    public function delete(Donation $donation): void
    {
        $donation->delete();
    }

    /**
     * Restore a soft-deleted donation.
     */
    public function restore(Donation $donation): Donation
    {
        $donation->restore();

        return $donation;
    }

    /**
     * Permanently delete a soft-deleted donation.
     */
    public function forceDelete(Donation $donation): void
    {
        $donation->forceDelete();
    }

    /**
     * Send (or resend) the receipt email for a donation.
     *
     * Best-effort: a broken/unconfigured mailer must never prevent the
     * donation itself from being recorded (same convention as
     * ContactService::submit()).
     */
    public function sendReceipt(Donation $donation): bool
    {
        if (! $donation->donor_email || $donation->status !== 'completed') {
            return false;
        }

        try {
            Mail::to($donation->donor_email)->send(new DonationReceipt($donation));

            $donation->forceFill(['receipt_sent_at' => now()])->save();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to send donation receipt email.', ['donation_id' => $donation->id, 'exception' => $e]);

            return false;
        }
    }
}

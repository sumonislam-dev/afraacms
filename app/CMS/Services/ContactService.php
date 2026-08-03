<?php

namespace App\CMS\Services;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Determine whether reCAPTCHA is configured and should be verified.
     */
    public function recaptchaEnabled(): bool
    {
        return (bool) (setting('recaptcha_site_key') && setting('recaptcha_secret'));
    }

    /**
     * Verify a submitted reCAPTCHA response token with Google.
     */
    public function verifyRecaptcha(?string $token): bool
    {
        if (! $this->recaptchaEnabled()) {
            return true;
        }

        if (! $token) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => setting('recaptcha_secret'),
            'response' => $token,
        ]);

        return (bool) $response->json('success', false);
    }

    /**
     * Record a submitted contact message and notify the site admin.
     *
     * Email delivery is best-effort: a broken/unconfigured mailer must
     * never prevent the message itself from being saved to the inbox.
     */
    public function submit(array $data, ?string $ip): ContactMessage
    {
        $message = ContactMessage::create([...$data, 'ip_address' => $ip]);

        if ($to = setting('contact_email')) {
            try {
                Mail::to($to)->send(new ContactMessageReceived($message));
            } catch (\Throwable $e) {
                Log::warning('Failed to send contact message notification email.', ['exception' => $e]);
            }
        }

        return $message;
    }
}

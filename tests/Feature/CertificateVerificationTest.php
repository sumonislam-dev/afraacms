<?php

namespace Tests\Feature;

use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_verify_page_shows_just_the_form_with_no_identifier(): void
    {
        $this->get(route('verify'))->assertOk()->assertDontSee('Valid Certificate');
    }

    public function test_a_valid_certificate_can_be_verified_by_certificate_number(): void
    {
        $certificate = Certificate::factory()->create(['recipient_name' => 'Jane Doe', 'status' => 'valid']);

        $response = $this->get(route('verify', ['code' => $certificate->certificate_number]));

        $response->assertOk()->assertSee('Valid Certificate')->assertSee('Jane Doe');
    }

    public function test_a_valid_certificate_can_be_verified_by_its_qr_code(): void
    {
        $certificate = Certificate::factory()->create(['recipient_name' => 'Jane Doe', 'status' => 'valid']);

        $response = $this->get(route('verify', ['code' => $certificate->verification_code]));

        $response->assertOk()->assertSee('Valid Certificate')->assertSee('Jane Doe');
    }

    public function test_a_revoked_certificate_shows_as_revoked(): void
    {
        $certificate = Certificate::factory()->revoked()->create();

        $response = $this->get(route('verify', ['code' => $certificate->certificate_number]));

        $response->assertOk()->assertSee('revoked')->assertDontSee('Valid Certificate');
    }

    public function test_an_unknown_code_shows_not_found(): void
    {
        $response = $this->get(route('verify', ['code' => 'CERT-2026-99999']));

        $response->assertOk()->assertSee('No certificate found');
    }
}

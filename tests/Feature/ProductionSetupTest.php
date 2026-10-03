<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_production_skips_the_test_login_and_demo_news(): void
    {
        $this->app['env'] = 'production';
        config(['admin.super_admin' => ['name' => 'Owner', 'email' => 'owner@example.org', 'password' => 'a-long-unique-password']]);

        // Exactly what docs/deployment.md tells you to run on the server.
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])->assertSuccessful();

        $this->assertTrue(User::where('email', 'owner@example.org')->first()->hasRole('Super Admin'));
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertSame(0, NewsPost::count());
    }

    public function test_seeding_locally_still_adds_the_test_login_and_demo_news(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertGreaterThan(0, NewsPost::count());
    }

    public function test_production_generates_https_links(): void
    {
        $this->app['env'] = 'production';
        (new AppServiceProvider($this->app))->boot();

        $this->assertStringStartsWith('https://', url('/about'));
    }

    public function test_other_environments_keep_the_request_scheme(): void
    {
        $this->assertStringStartsWith('http://', url('/about'));
    }
}

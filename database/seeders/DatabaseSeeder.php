<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            MenuSeeder::class,
            PagesSeeder::class,
        ]);

        // Demo news posts and a test login with the factory's known password
        // ("password") must never reach a live site.
        if (app()->isProduction()) {
            return;
        }

        $this->call(NewsSeeder::class);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $testUser->assignRole('Editor');
    }
}

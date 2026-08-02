<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed a default header menu so the frontend has something to render.
     */
    public function run(): void
    {
        $menu = Menu::firstOrCreate(
            ['slug' => 'header'],
            ['name' => 'Header Menu']
        );

        if ($menu->items()->doesntExist()) {
            $menu->items()->create([
                'label' => 'Home',
                'type' => 'internal',
                'url' => '/',
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Support\LandingPageDefaults;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::query()->updateOrCreate(
            ['slug' => 'home'],
            LandingPageDefaults::data(),
        );
    }
}

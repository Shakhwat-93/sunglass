<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_from_database_content(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $page = LandingPage::query()->where('slug', 'home')->first();

        $this->assertNotNull($page);
        $response->assertSee($page->brand_name, false);
    }
}

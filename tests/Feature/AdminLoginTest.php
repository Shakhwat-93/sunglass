<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Support\LandingPageDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_and_open_dashboard(): void
    {
        $this->setAdminCredentials();

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $dashboard = $this->withSession([
            'admin_authenticated' => true,
            'admin_email' => 'admin@example.com',
        ])->get('/admin');

        $dashboard->assertOk();
        $dashboard->assertSee('Total Orders');
        $dashboard->assertSeeText('Choose a page');
        $dashboard->assertSeeText('Orders');
    }

    public function test_admin_can_update_landing_content_from_dashboard(): void
    {
        LandingPage::home();

        $payload = [
            'meta_title' => 'Updated premium title',
            'meta_description' => LandingPageDefaults::data()['meta_description'],
            'brand_name' => LandingPageDefaults::data()['brand_name'],
            'brand_accent' => LandingPageDefaults::data()['brand_accent'],
            'hero_headline' => 'Updated hero headline',
            'hero_image' => LandingPageDefaults::data()['hero_image'],
            'floating_cta_label' => LandingPageDefaults::data()['floating_cta_label'],
        ];

        $response = $this->withSession([
            'admin_authenticated' => true,
            'admin_email' => 'admin@example.com',
        ])->put(route('admin.sections.update', 'brand-hero'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Brand & Hero updated successfully.');

        $this->assertSame('Updated premium title', LandingPage::home()->meta_title);
        $this->assertSame('Updated hero headline', LandingPage::home()->hero_headline);
    }

    private function setAdminCredentials(): void
    {
        putenv('ADMIN_EMAIL=admin@example.com');
        putenv('ADMIN_PASSWORD=secret-password');
        $_ENV['ADMIN_EMAIL'] = 'admin@example.com';
        $_ENV['ADMIN_PASSWORD'] = 'secret-password';
        $_SERVER['ADMIN_EMAIL'] = 'admin@example.com';
        $_SERVER['ADMIN_PASSWORD'] = 'secret-password';
    }
}

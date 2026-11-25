<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_in_default_indonesian_locale_with_contact_email(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('TemuKampus');
        $response->assertSee('nixresponding@gmail.com');
        $response->assertSee('Temukan barang hilang di kampus tanpa panik dan tanpa bocor privasi.');
        $response->assertDontSee('[DEMO]');
    }

    public function test_docs_page_renders_without_auth_and_contains_customization_notice(): void
    {
        $response = $this->get('/docs');

        $response->assertOk();
        $response->assertSee('nixresponding@gmail.com');
        $response->assertSee('Raka Pratama');
        $response->assertSee('Dr. Ratna Wijaya');
        $response->assertSee('Bapak Hendra Kusuma');
        $response->assertSee('Nadia Larasati');
        $response->assertSee('35');
        $response->assertSee('notification_logs');
    }

    public function test_public_pages_switch_cleanly_to_english(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/locale', ['locale' => 'en'])->assertRedirect();

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Recover lost campus belongings fast');
        $home->assertDontSee('landing.hero.title');
        $home->assertDontSee('app.brand');

        $docs = $this->get('/docs');
        $docs->assertOk();
        $docs->assertSee('Need something more tailored?');
        $docs->assertDontSee('docs.title');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoQuickLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_demo_login_page_displays_four_personas_when_enabled(): void
    {
        $response = $this->get('/demo/login');

        $response->assertOk();
        $response->assertSee('Raka Pratama');
        $response->assertSee('Dr. Ratna Wijaya');
        $response->assertSee('Bapak Hendra Kusuma');
        $response->assertSee('Nadia Larasati');
        $response->assertDontSee('type="password"', false);
    }

    public function test_each_persona_can_quick_login_and_redirect_to_correct_dashboard(): void
    {
        $expectedRedirects = [
            'student' => route('dashboard'),
            'staff' => route('dashboard'),
            'custodian' => route('custodian.desk'),
            'admin' => route('admin.analytics'),
        ];

        foreach ($expectedRedirects as $persona => $targetUrl) {
            $oldSessionId = session()->getId();

            $response = $this->post('/demo/quick-login', ['persona' => $persona]);
            $response->assertRedirect($targetUrl);

            $user = User::where('email', config("demo.personas.{$persona}.email"))->firstOrFail();
            $this->assertAuthenticatedAs($user);
            $this->assertNotSame($oldSessionId, session()->getId());

            $this->post('/logout');
        }
    }

    public function test_invalid_persona_is_rejected(): void
    {
        $response = $this->post('/demo/quick-login', ['persona' => 'superadmin']);

        $response->assertSessionHasErrors('persona');
        $this->assertGuest();
    }

    public function test_logout_invalidates_session_and_redirects_to_demo_login(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();

        $response = $this->actingAs($student)->post('/logout');

        $response->assertRedirect(route('demo.login'));
        $this->assertGuest();
    }

    public function test_demo_routes_return_404_when_demo_mode_disabled(): void
    {
        config(['demo.enabled' => false]);

        $this->get('/demo/login')->assertNotFound();
        $this->post('/demo/quick-login', ['persona' => 'student'])->assertNotFound();

        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $this->actingAs($student)->post('/logout')->assertRedirect(route('public.home'));
        $this->assertGuest();
    }

    public function test_guest_visiting_protected_route_redirects_cleanly_without_route_login_error(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('demo.login'));
    }
}

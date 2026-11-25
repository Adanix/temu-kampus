<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_default_locale_is_indonesian(): void
    {
        $this->get('/')->assertOk();

        $this->assertSame('id', app()->getLocale());
    }

    public function test_switching_locale_persists_in_session_and_user_preference(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();

        $this->actingAs($student)
            ->post('/locale', ['locale' => 'en'])
            ->assertRedirect();

        $this->assertSame('en', session('locale'));
        $this->assertSame('en', $student->fresh()->preferred_locale);

        $this->actingAs($student)->get('/dashboard')->assertSee('Personal Dashboard');

        $this->actingAs($student)
            ->post('/locale', ['locale' => 'id'])
            ->assertRedirect();

        $this->assertSame('id', session('locale'));
        $this->assertSame('id', $student->fresh()->preferred_locale);
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->post('/locale', ['locale' => 'fr']);

        $response->assertSessionHasErrors('locale');
    }
}

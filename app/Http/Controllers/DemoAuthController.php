<?php

namespace App\Http\Controllers;

use App\Enums\DemoPersona;
use App\Http\Requests\QuickLoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoAuthController extends Controller
{
    public function show(Request $request): View
    {
        return view('demo.login', [
            'personas' => config('demo.personas'),
            'currentUser' => $request->user(),
        ]);
    }

    public function login(QuickLoginRequest $request): RedirectResponse
    {
        $personaKey = $request->validated('persona');
        $email = config("demo.personas.{$personaKey}.email");

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        if (! $request->session()->has('locale')) {
            $request->session()->put('locale', $user->preferred_locale ?? config('app.locale', 'id'));
        }

        return redirect()->route(DemoPersona::from($personaKey)->landingRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        $locale = $request->session()->get('locale');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (in_array($locale, ['id', 'en'], true)) {
            $request->session()->put('locale', $locale);
        }

        if (config('demo.enabled')) {
            return redirect()->route('demo.login');
        }

        return redirect()->route('public.home');
    }
}

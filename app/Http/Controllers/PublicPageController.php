<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'demoEnabled' => (bool) config('demo.enabled'),
            'contactEmail' => (string) config('demo.contact_email'),
        ]);
    }

    public function docs(): View
    {
        return view('public.docs', [
            'demoEnabled' => (bool) config('demo.enabled'),
            'contactEmail' => (string) config('demo.contact_email'),
            'personas' => config('demo.personas'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwitchLocaleRequest;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Persist the selected application locale ('id' or 'en') in session and user preferences.
     */
    public function update(SwitchLocaleRequest $request): RedirectResponse
    {
        $locale = $request->validated('locale');

        $request->session()->put('locale', $locale);

        if ($request->user() !== null) {
            $request->user()->update(['preferred_locale' => $locale]);
        }

        $previous = url()->previous();

        if ($previous !== '' && $previous !== url()->current()) {
            return redirect()->back();
        }

        return redirect()->route('public.home');
    }
}

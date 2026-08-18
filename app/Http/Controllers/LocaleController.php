<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $locale = $request->validate([
            'locale' => ['required', 'string', Rule::in(config('app.available_locales', ['lv', 'en']))],
        ])['locale'];

        $request->session()->put('locale', $locale);

        return back()
            ->withCookie(cookie()->forever('locale', $locale));
    }
}

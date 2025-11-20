<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Switch application language
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'en');

        // Validate locale
        if (!in_array($locale, ['en', 'bn'])) {
            $locale = 'en';
        }

        // Store locale in session
        Session::put('locale', $locale);

        // If user is authenticated, store in database
        if (Auth::check()) {
            Auth::user()->update(['language' => $locale]);
        }

        // Redirect back
        return redirect()->back();
    }
}

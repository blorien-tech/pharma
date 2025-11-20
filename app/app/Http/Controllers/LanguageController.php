<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        // Redirect back
        return redirect()->back();
    }
}

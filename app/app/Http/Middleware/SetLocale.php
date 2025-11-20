<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = 'en'; // Default locale

        // Priority 1: Get from authenticated user's database preference
        if (Auth::check() && Auth::user()->language) {
            $locale = Auth::user()->language;
        }
        // Priority 2: Get from session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }

        // Ensure locale is either 'en' or 'bn'
        if (!in_array($locale, ['en', 'bn'])) {
            $locale = 'en';
        }

        // Set application locale
        App::setLocale($locale);

        // Sync session with database preference for authenticated users
        if (Auth::check() && Session::get('locale') !== $locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}

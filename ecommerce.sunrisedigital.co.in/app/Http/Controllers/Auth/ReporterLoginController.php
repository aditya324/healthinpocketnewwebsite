<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ReporterLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReporterLoginController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        if (Auth::guard('reporter')->check()) {
            return redirect()->route('reports.home');
        }

        return response()
            ->view('auth.reporter-login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function store(ReporterLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('reports.home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('reporter')->logout();

        $request->session()->regenerateToken();

        return redirect()->route('reports.login');
    }
}

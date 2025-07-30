<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // التحقق من تفعيل الإيميل
        // if (is_null(Auth::user()->email_verified_at)) {
        //     Auth::logout();
        //     return redirect()->route('verification.notice', ['email' => $request->email])
        //         ->with('error', 'يجب تفعيل البريد الإلكتروني أولاً. تم إرسال كود التحقق إلى بريدك.');
        // }

        $request->session()->regenerate();

        // التحقق من دور المستخدم وتوجيهه للواجهة المناسبة
        if (Auth::user()->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

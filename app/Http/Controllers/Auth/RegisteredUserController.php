<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'currency' => 'ليرة سورية', // تعيين العملة الافتراضية
            'email_verified_at' => null, // لا تفعل البريد الإلكتروني تلقائياً
        ]);

        // إنشاء صندوق خاص بالمستخدم الجديد
        \App\Models\CashBox::create([
            'user_id' => $user->id,
            'name' => 'الصندوق الرئيسي',
            'initial_balance' => 0,
            'current_balance' => 0
        ]);

        // تعيين دور user تلقائياً
        $user->assignRole('user');

        // إرسال رسالة التحقق من البريد الإلكتروني
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('status', 'تم إنشاء الحساب بنجاح! يرجى التحقق من بريدك الإلكتروني لتأكيد الحساب.');
    }
}

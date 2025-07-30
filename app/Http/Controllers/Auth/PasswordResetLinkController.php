<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset code request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // إنشاء كود تحقق عشوائي (6 أرقام)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // حفظ الكود في قاعدة البيانات
        PasswordResetCode::updateOrCreate(
            ['email' => $email],
            [
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(15), // الكود صالح لمدة 15 دقيقة
            ]
        );

        // إرسال الكود عبر البريد الإلكتروني
        Mail::send('emails.password-reset-code', [
            'user' => $user,
            'code' => $code
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject('كود إعادة تعيين كلمة المرور - نظام إدارة الفواتير');
        });

        return redirect()->route('password.reset.code')
            ->with('status', 'تم إرسال كود التحقق إلى بريدك الإلكتروني.')
            ->with('email', $email);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->email;
        $code = $request->code;

        // التحقق من الكود
        $resetRecord = PasswordResetCode::where('email', $email)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord || !Hash::check($code, $resetRecord->code)) {
            return back()->withErrors(['code' => 'الكود غير صحيح أو منتهي الصلاحية.']);
        }

        // تحديث كلمة المرور
        $user = User::where('email', $email)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // حذف كود التحقق
        $resetRecord->delete();

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول.');
    }
}

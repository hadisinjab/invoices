<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    /**
     * عرض صفحة إدخال كود التحقق
     */
    public function show(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('register')->with('error', 'البريد الإلكتروني مطلوب');
        }
        return view('auth.verify-email', compact('email'));
    }

    /**
     * التحقق من الكود وتفعيل الحساب
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:4',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.exists' => 'البريد الإلكتروني غير مسجل',
            'code.required' => 'كود التحقق مطلوب',
            'code.digits' => 'كود التحقق يجب أن يكون 4 أرقام',
        ]);

        $user = User::where('email', $request->email)->first();
        $verification = EmailVerification::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('expires_at', '>=', now())
            ->latest()->first();

        if (!$verification) {
            return back()->with('error', 'كود التحقق غير صحيح أو منتهي الصلاحية')->withInput();
        }

        // تفعيل الحساب
        $user->email_verified_at = now();
        $user->save();
        // حذف جميع أكواد التحقق لهذا المستخدم
        EmailVerification::where('user_id', $user->id)->delete();

        // تسجيل الدخول تلقائياً
        Auth::login($user);

        Log::info('تم تفعيل حساب جديد', ['email' => $user->email]);

        return redirect()->route('dashboard')->with('status', 'تم تفعيل الحساب بنجاح!');
    }
}

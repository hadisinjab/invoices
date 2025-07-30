<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle(Request $request, Closure $next): Response
    {
        // التحقق من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // التحقق من أن المستخدم لديه دور الإدمن
        if (!Auth::user()->hasRole('admin')) {
            // إذا كان المستخدم يحاول الوصول لواجهة الإدمن، أعد توجيهه للوحة الرئيسية
            if ($request->is('admin*')) {
                return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
            }
        }

        return $next($request);
    }
}

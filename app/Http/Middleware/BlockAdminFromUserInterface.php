<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockAdminFromUserInterface
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
        if (Auth::user() && Auth::user()->hasRole('admin')) {
            // منع الأدمن من الوصول لأي مسار في المجموعة العامة
            return redirect()->route('admin.dashboard')
                ->with('error', 'الأدمن لا يمكنه الوصول لهذه الصفحة. يرجى استخدام لوحة الإدارة.');
        }

        return $next($request);
    }
}

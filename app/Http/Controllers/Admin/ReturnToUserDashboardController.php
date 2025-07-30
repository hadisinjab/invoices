<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnToUserDashboardController extends Controller
{
    /**
     * العودة للداشبورد الأساسي مع إزالة دور الأدمن مؤقتاً
     */
    public function returnToUserDashboard(Request $request)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم أدمن
        if (!$user->hasRole('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'ليس لديك صلاحية للوصول لهذه الصفحة');
        }

        // إزالة دور الأدمن مؤقتاً (يمكن إعادته لاحقاً)
        $user->removeRole('admin');

        // إضافة دور المستخدم العادي إذا لم يكن موجوداً
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        return redirect()->route('dashboard')
            ->with('success', 'تم العودة للداشبورد الأساسي بنجاح');
    }

    /**
     * إعادة تعيين دور الأدمن
     */
    public function restoreAdminRole(Request $request)
    {
        $user = Auth::user();

        // التحقق من عدم وجود أدمن آخر
        $existingAdmin = \App\Models\User::role('admin')->where('id', '!=', $user->id)->first();
        if ($existingAdmin) {
            return redirect()->back()
                ->with('error', 'لا يمكن إعادة تعيين دور الأدمن لأن هناك أدمن آخر موجود');
        }

        // إعادة تعيين دور الأدمن
        $user->assignRole('admin');

        return redirect()->route('admin.dashboard')
            ->with('success', 'تم إعادة تعيين دور الأدمن بنجاح');
    }
}

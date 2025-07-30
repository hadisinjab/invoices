<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Client;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * عرض لوحة الإدارة الرئيسية
     */
    public function index()
    {
        // إعادة توجيه الإدمن مباشرة إلى صفحة إدارة المستخدمين
        return redirect()->route('admin.users.index');
    }

    /**
     * الحصول على إحصائيات النظام
     */
    private function getStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'total_invoices' => Invoice::count(),
            'total_clients' => Client::count(),
            'pending_requests' => 0,
            'invoices_this_month' => Invoice::whereMonth('created_at', now()->month)->count(),
            'new_clients' => Client::whereMonth('created_at', now()->month)->count(),
            'new_users' => User::whereMonth('created_at', now()->month)->count(),
            'total_sales' => Invoice::sum('total_after_discount') ?? 0,
        ];
    }

    /**
     * الحصول على طلبات تغيير كلمة المرور الحديثة
     */
    private function getRecentRequests()
    {
        return collect([]);
    }

    /**
     * الحصول على المستخدمين الجدد
     */
    private function getRecentUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}

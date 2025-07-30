@extends('admin.layouts.app')

@section('title', 'لوحة الإدارة')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="admin-stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المستخدمين</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="admin-stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">طلبات تغيير كلمة المرور</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600">في الانتظار</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="admin-stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الفواتير</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_invoices'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="admin-stats-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي العملاء</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_clients'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- طلبات تغيير كلمة المرور العاجلة -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-exclamation-triangle text-yellow-600 ml-2"></i>
                طلبات تغيير كلمة المرور العاجلة
            </h3>

                <i class="fas fa-eye ml-2"></i>
                عرض الكل
            </a>
        </div>

        @if(isset($recentRequests) && $recentRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>البريد الإلكتروني</th>
                            <th>اسم المستخدم</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                            <tr>
                                <td>{{ $request->email }}</td>
                                <td>{{ $request->name ?? 'غير محدد' }}</td>
                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                <td>

                                       class="admin-btn-primary text-sm">
                                        <i class="fas fa-eye ml-1"></i>
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-600">لا توجد طلبات عاجلة لتغيير كلمة المرور</p>
            </div>
        @endif
    </div>

    <!-- آخر المستخدمين المسجلين -->
    <div class="admin-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-user-plus text-blue-600 ml-2"></i>
                آخر المستخدمين المسجلين
            </h3>
            <a href="{{ route('admin.users.index') }}" class="admin-btn-primary">
                <i class="fas fa-users ml-2"></i>
                إدارة المستخدمين
            </a>
        </div>

        @if(isset($recentUsers) && $recentUsers->count() > 0)
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>تاريخ التسجيل</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle ml-1"></i>
                                        نشط
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">لا يوجد مستخدمين مسجلين حديثاً</p>
            </div>
        @endif
    </div>

    <!-- إحصائيات النظام -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-blue-600 ml-2"></i>
                إحصائيات النظام
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">المستخدمين النشطين</span>
                    <span class="font-semibold">{{ $stats['active_users'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">الفواتير هذا الشهر</span>
                    <span class="font-semibold">{{ $stats['invoices_this_month'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">العملاء الجدد</span>
                    <span class="font-semibold">{{ $stats['new_clients'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">إجمالي المبيعات</span>
                    <span class="font-semibold">{{ $stats['total_sales'] ?? 0 }} ريال</span>
                </div>
            </div>
        </div>

        <div class="admin-card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-tasks text-green-600 ml-2"></i>
                المهام العاجلة
            </h3>
            <div class="space-y-3">
                @if(isset($stats['pending_requests']) && $stats['pending_requests'] > 0)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-key text-yellow-600 ml-2"></i>
                            <span class="text-yellow-800">معالجة طلبات تغيير كلمة المرور</span>
                        </div>
                        <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $stats['pending_requests'] }}
                        </span>
                    </div>
                @endif

                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-600 ml-2"></i>
                        <span class="text-blue-800">مراجعة المستخدمين الجدد</span>
                    </div>
                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                        {{ $stats['new_users'] ?? 0 }}
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-green-600 ml-2"></i>
                        <span class="text-green-800">مراجعة إحصائيات النظام</span>
                    </div>
                    <i class="fas fa-arrow-left text-green-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// تحديث الإحصائيات كل 30 ثانية
setInterval(function() {

        .then(response => response.json())
        .then(data => {
            // تحديث عدد الطلبات المعلقة
            const pendingElement = document.querySelector('.admin-stats-card:nth-child(2) .text-2xl');
            if (pendingElement) {
                pendingElement.textContent = data.pending;
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}, 30000);
</script>
@endsection

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashBox;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // إنشاء الصلاحيات للمشرف
        $adminPermissions = [
            'invoices.view_all',
            'invoices.edit_any',
            'invoices.delete_any',
            'invoices.change_status',
            'invoices.print_any',
            'users.list',
            'users.activate',
            'users.deactivate',
            'users.assign_roles',
            'clients.view_any',
            'clients.edit_any',
            'clients.delete_any',
            'system.view_stats',
            'system.generate_reports',
            'expenses.view_all',
            'expenses.edit_any',
            'expenses.delete_any',
            'cash_box.view_all',
            'cash_box.edit_any',
            'products.view_all',
            'products.edit_any',
            'products.delete_any',
        ];

        foreach ($adminPermissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm]);
            $adminRole->givePermissionTo($permission);
        }

        // إنشاء الصلاحيات للمستخدم العادي
        $userPermissions = [
            // الفواتير
            'invoices.create',
            'invoices.view_own',
            'invoices.edit_own',
            'invoices.delete_own',
            'invoices.print_own',
            // العملاء
            'clients.add',
            'clients.view_own',
            'clients.edit_own',
            'clients.delete_own',
            // المصاريف
            'expenses.create',
            'expenses.view_own',
            'expenses.edit_own',
            'expenses.delete_own',
            // الصندوق
            'cash_box.view_own',
            'cash_box.transactions',
            // المنتجات
            'products.create',
            'products.view_own',
            'products.edit_own',
            'products.delete_own',
            // لوحة التحكم والملف الشخصي
            'dashboard.view_summary',
            'profile.edit',
        ];

        foreach ($userPermissions as $perm) {
            $permission = Permission::firstOrCreate(['name' => $perm]);
            $userRole->givePermissionTo($permission);
        }

        // إنشاء مستخدم افتراضي
        $admin = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Admin',
                'password' => bcrypt('password'),
                'currency' => 'SAR'
            ]
        );
        $admin->assignRole('admin');

        // إنشاء مستخدم عادي
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'currency' => 'SAR'
            ]
        );
        $user->assignRole('user');

        // إنشاء صندوق افتراضي
        $firstUserId = \App\Models\User::min('id');
        CashBox::create([
            'user_id' => $firstUserId,
            'name' => 'الصندوق الرئيسي',
            'initial_balance' => 0,
            'current_balance' => 0,
            'last_calculated_balance' => 0
        ]);

        // إنشاء بيانات تجريبية
        $this->call(SampleDataSeeder::class);
    }
}

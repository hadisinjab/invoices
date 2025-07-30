<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء دور الإدمن إذا لم يكن موجوداً
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
            $this->command->info('Admin role created successfully.');
        }

        // إنشاء دور المستخدم العادي إذا لم يكن موجوداً
        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user']);
            $this->command->info('User role created successfully.');
        }

        $this->command->info('Roles setup completed successfully!');
        $this->command->info('Note: Admin users must be created manually through the admin panel.');
    }
}

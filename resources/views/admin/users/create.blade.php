@extends('admin.layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="admin-card p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-plus mr-2"></i>
            إضافة مستخدم جديد
        </h1>
        <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الاسم -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    الاسم الكامل
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       class="admin-input @error('name') border-red-500 @enderror"
                       placeholder="أدخل الاسم الكامل"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    البريد الإلكتروني
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="admin-input @error('email') border-red-500 @enderror"
                       placeholder="example@domain.com"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- كلمة المرور -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    كلمة المرور
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="admin-input @error('password') border-red-500 @enderror"
                       placeholder="أدخل كلمة المرور"
                       required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- تأكيد كلمة المرور -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    تأكيد كلمة المرور
                </label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="admin-input"
                       placeholder="أعد إدخال كلمة المرور"
                       required>
            </div>


        </div>

        <!-- ملاحظة حول الدور -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 ml-2"></i>
                <span class="text-blue-800 text-sm">
                    <strong>ملاحظة:</strong> سيتم تعيين المستخدم الجديد كـ "مستخدم عادي" تلقائياً. لا يمكن إنشاء أدمن جديد من هذه الواجهة.
                </span>
            </div>
        </div>

        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
                إلغاء
            </a>
            <button type="submit" class="admin-btn-primary">
                <i class="fas fa-save ml-2"></i>
                حفظ المستخدم
            </button>
        </div>
    </form>
</div>
@endsection

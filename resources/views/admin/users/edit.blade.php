@extends('admin.layouts.app')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="admin-card p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-edit mr-2"></i>
            تعديل المستخدم: {{ $user->name }}
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

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- الاسم -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    الاسم الكامل
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
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
                       value="{{ old('email', $user->email) }}"
                       class="admin-input @error('email') border-red-500 @enderror"
                       placeholder="example@domain.com"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>



            <!-- معلومات إضافية -->
            <div class="md:col-span-2">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-700 mb-3">معلومات إضافية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-600">تاريخ التسجيل:</span>
                            <span class="text-gray-800">{{ $user->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">آخر تحديث:</span>
                            <span class="text-gray-800">{{ $user->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">حالة التحقق:</span>
                            <span class="text-gray-800">
                                @if($user->email_verified_at)
                                    <span class="text-green-600">✓ محقق</span>
                                @else
                                    <span class="text-red-600">✗ غير محقق</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">الدور الحالي:</span>
                            <span class="text-gray-800">
                                @if($user->hasRole('admin'))
                                    <span class="text-blue-600 font-medium">إدمن</span>
                                @else
                                    <span class="text-gray-600">مستخدم</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
                إلغاء
            </a>
            <button type="submit" class="admin-btn-primary">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection

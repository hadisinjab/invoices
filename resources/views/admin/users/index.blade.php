@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="admin-card p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-users mr-2"></i>
            إدارة المستخدمين
        </h1>
        <a href="{{ route('admin.users.create') }}" class="admin-btn-primary">
            <i class="fas fa-user-plus ml-2"></i>
            إضافة مستخدم جديد
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->hasRole('admin'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-shield-alt ml-1"></i> إدمن
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-user ml-1"></i> مستخدم
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn-primary text-xs mr-1">
                                <i class="fas fa-edit ml-1"></i>تعديل
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn-danger text-xs"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                        <i class="fas fa-trash ml-1"></i>حذف
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection

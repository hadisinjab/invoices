<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة الإدارة') - نظام الفواتير</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <div class="admin-sidebar-logo">
                    <i class="fas fa-shield-alt"></i>
                    <span>لوحة الإدارة</span>
                </div>
            </div>

            <nav class="admin-sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>الرئيسية</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>إدارة المستخدمين</span>
           


            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <h1>@yield('title', 'لوحة الإدارة')</h1>

                <div class="admin-user-menu">
                    <div class="admin-user-info">
                        <div class="admin-user-name">{{ auth()->user()->name }}</div>
                        <div class="admin-user-role">مدير النظام</div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="admin-logout-btn">
                            <i class="fas fa-sign-out-alt ml-1"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            @if(session('success'))
                <div class="admin-message success">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="admin-message error">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // إضافة تفاعلات JavaScript للواجهة
        document.addEventListener('DOMContentLoaded', function() {
            // تفعيل القوائم المنسدلة
            const dropdowns = document.querySelectorAll('[data-dropdown]');
            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('[data-dropdown-trigger]');
                const content = dropdown.querySelector('[data-dropdown-content]');

                if (trigger && content) {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        content.classList.toggle('hidden');
                    });
                }
            });

            // إغلاق القوائم المنسدلة عند النقر خارجها
            document.addEventListener('click', function(e) {
                if (!e.target.closest('[data-dropdown]')) {
                    document.querySelectorAll('[data-dropdown-content]').forEach(content => {
                        content.classList.add('hidden');
                    });
                }
            });
        });
    </script>
</body>
</html>

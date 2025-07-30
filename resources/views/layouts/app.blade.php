<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'لوحة الفواتير')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        /* خلفية متدرجة للشريط الجانبي */
        .sidebar-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        .sidebar-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .sidebar-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .sidebar-item::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: right 0.6s;
        }
        .sidebar-item:hover::before {
            right: 100%;
        }
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .icon-pulse {
            animation: iconPulse 2s infinite;
        }
        @keyframes iconPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            max-height: 0;
            overflow: hidden;
        }
        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            max-height: 200px;
        }
        .animated-border {
            position: relative;
            overflow: hidden;
        }
        .animated-border::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.4s ease;
        }
        .animated-border:hover::after {
            width: 100%;
        }
        .loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
            z-index: 9999;
        }
        .loading-bar.active {
            transform: scaleX(1);
        }
        .wave-effect {
            position: relative;
            overflow: hidden;
        }
        .wave-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }
        .wave-effect:hover::before {
            left: 100%;
        }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            padding: 0 6px;
            animation: bounce 2s infinite;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.5);
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .light-effect {
            position: relative;
            overflow: hidden;
        }
        .light-effect::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: rotate 4s linear infinite;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .light-effect:hover::before {
            opacity: 1;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }
            .sidebar-mobile.show {
                transform: translateX(0);
            }
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(17, 153, 142, 0.4);
        }
        /* --- تثبيت الشريط الجانبي --- */
        #sidebar {
            position: fixed !important;
            top: 0;
            right: 0;
            height: 100vh;
            z-index: 30;
        }
        .sidebar-scrollable {
            height: 100vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        main[role="main"], main.flex-1 {
            margin-right: 18rem; /* نفس عرض الشريط الجانبي */
        }
        @media (max-width: 1024px) {
            #sidebar {
                width: 16rem;
            }
            main[role="main"], main.flex-1 {
                margin-right: 0;
            }
        }
        .nav-item-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .nav-item-content {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }
        .nav-item-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }
        .add-btn {
            position: relative;
            z-index: 10;
        }
    </style>
    @stack('styles')
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans">
    <!-- شريط التحميل -->
    <div class="loading-bar" id="loadingBar"></div>

    <div class="min-h-screen flex relative">
        <!-- الشريط الجانبي -->
        <aside class="w-72 sidebar-gradient shadow-2xl fixed md:relative h-full md:h-screen sidebar-mobile z-30" id="sidebar">
            <div class="sidebar-scrollable h-full flex flex-col">
                <div class="p-6 flex-shrink-0">
                    <!-- معلومات المستخدم -->
                    <div class="mb-8 fade-in">
                        <div class="glass-effect rounded-xl p-4 text-center text-white">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <i class="fas fa-user text-2xl icon-pulse"></i>
                            </div>
                            <div class="relative">
                                <button id="user-menu-btn" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-white bg-opacity-10 hover:bg-opacity-20 font-bold text-lg transition-all duration-300 wave-effect">
                                    <span>{{ Auth::user()->name ?? 'مستخدم' }}</span>
                                    <i class="fas fa-chevron-down text-sm transition-transform duration-300" id="chevron"></i>
                                </button>
                                <div id="user-menu" class="dropdown-menu absolute right-0 mt-3 w-full bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50">
                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-200">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>تسجيل الخروج</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- عنوان اللوحة -->
                    <h2 class="text-2xl font-bold mb-8 text-center text-white fade-in light-effect">
                        <i class="fas fa-tachometer-alt ml-2"></i>
                        لوحة التحكم
                    </h2>
                </div>

                <!-- قائمة التنقل -->
                <nav class="flex-1 px-6 pb-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-home text-xl"></i>
                        <span class="font-medium">الرئيسية</span>
                    </a>

                    <div class="sidebar-item rounded-xl text-white fade-in">
                        <div class="nav-item-wrapper p-4">
                            <div class="nav-item-content">
                                <i class="fas fa-file-invoice text-xl"></i>
                                <a href="{{ route('invoices.index') }}" class="font-medium hover:text-white">الفواتير</a>
                            </div>
                            <div class="nav-item-actions">
                                <div class="relative">
                                    <span class="notification-badge">{{ $invoicesCount }}</span>
                                </div>
                                <a href="{{ route('invoices.create') }}"
                                   class="add-btn btn-secondary w-8 h-8 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300"
                                   title="إضافة فاتورة جديدة">
                                    <i class="fas fa-plus text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('expenses.index') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-receipt text-xl"></i>
                        <span class="font-medium">المصاريف</span>
                    </a>

                    <div class="sidebar-item rounded-xl text-white fade-in">
                        <div class="nav-item-wrapper p-4">
                            <div class="nav-item-content">

                                <a href="{{ route('clients.index') }}" class="font-medium hover:text-white">
                                    <i class="fas fa-users text-xl"></i>
                                    <span class="font-medium">العملاء</span>
                                </a>
                            </div>
                            <div class="nav-item-actions">
                                <div class="relative">
                                    <span class="notification-badge">{{ $clientsCount }}</span>
                                </div>
                                <a href="{{ route('clients.create') }}"
                                   class="add-btn btn-secondary w-8 h-8 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300"
                                   title="إضافة عميل جديد">
                                    <i class="fas fa-plus text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-box text-xl"></i>
                        <span class="font-medium">منتجاتي</span>
                    </a>

                    <a href="{{ route('cash-box.index') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-cash-register text-xl"></i>
                        <span class="font-medium">الصندوق</span>
                    </a>

                    <a href="{{ route('reports.daily') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-chart-bar text-xl"></i>
                        <span class="font-medium">التقارير</span>
                    </a>

                    @if(auth()->user() && auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                            <i class="fas fa-shield-alt text-xl"></i>
                            <span class="font-medium">لوحة الإدارة</span>
                        </a>

                            <i class="fas fa-key text-xl"></i>
                            <span class="font-medium">طلبات تغيير كلمة المرور</span>
                        </a>
                    @endif



                    <a href="{{ route('profile.edit') }}" class="sidebar-item flex items-center gap-4 p-4 rounded-xl text-white hover:text-white animated-border fade-in">
                        <i class="fas fa-cog text-xl"></i>
                        <span class="font-medium">الإعدادات</span>
                    </a>
                </nav>

                <!-- معلومات النظام -->
                <div class="p-6 flex-shrink-0">
                    <div class="glass-effect rounded-xl p-4 text-center text-white text-sm fade-in">
                        <i class="fas fa-info-circle mb-2"></i>
                        <p>نظام الفواتير v2.0</p>
                        <p class="text-xs opacity-75">جميع الحقوق محفوظة</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- المحتوى الرئيسي -->
        <main class="flex-1 min-h-screen">
            <!-- شريط علوي للهاتف المحمول -->
            <div class="md:hidden bg-white shadow-sm border-b border-gray-200 p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">لوحة الفواتير</h1>
                <button id="mobile-menu-btn" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
            </div>

            <!-- المحتوى -->
            <div class="p-6 md:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- خلفية مظلمة للهاتف المحمول -->
    <div class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-20 opacity-0 pointer-events-none transition-opacity duration-300" id="mobile-backdrop"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تفعيل شريط التحميل
            const loadingBar = document.getElementById('loadingBar');
            loadingBar.classList.add('active');
            setTimeout(() => {
                loadingBar.classList.remove('active');
            }, 1000);

            // قائمة المستخدم المنسدلة
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userMenu = document.getElementById('user-menu');
            const chevron = document.getElementById('chevron');

            if (userMenuBtn && userMenu && chevron) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isOpen = userMenu.classList.contains('show');

                    // إغلاق القائمة إذا كانت مفتوحة، وفتحها إذا كانت مغلقة
                    if (isOpen) {
                        userMenu.classList.remove('show');
                        chevron.style.transform = 'rotate(0deg)';
                    } else {
                        userMenu.classList.add('show');
                        chevron.style.transform = 'rotate(180deg)';
                    }
                });

                // إغلاق القائمة عند النقر خارجها
                document.addEventListener('click', function(e) {
                    if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.remove('show');
                        chevron.style.transform = 'rotate(0deg)';
                    }
                });

                // منع إغلاق القائمة عند النقر داخلها
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // قائمة الهاتف المحمول
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const mobileBackdrop = document.getElementById('mobile-backdrop');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    const isOpen = sidebar.classList.contains('show');
                    mobileBackdrop.style.opacity = isOpen ? '1' : '0';
                    mobileBackdrop.style.pointerEvents = isOpen ? 'auto' : 'none';
                });

                mobileBackdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    mobileBackdrop.style.opacity = '0';
                    mobileBackdrop.style.pointerEvents = 'none';
                });
            }

            // تأثير الظهور التدريجي للعناصر
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
            });

            // تأثير النقر على الروابط
            const sidebarLinks = document.querySelectorAll('.sidebar-item');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // تجنب تأثير النقر على الأزرار الفرعية
                    if (e.target.closest('.add-btn')) {
                        return;
                    }

                    // إضافة تأثير النقر
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = e.offsetX + 'px';
                    ripple.style.top = e.offsetY + 'px';
                    ripple.style.width = '20px';
                    ripple.style.height = '20px';
                    ripple.style.pointerEvents = 'none';

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // تأثير التمرير السلس
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // إضافة تأثير الـ ripple للـ CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>

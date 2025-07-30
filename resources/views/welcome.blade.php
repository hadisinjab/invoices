<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك - نظام إدارة الفواتير</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    <!-- شريط التحميل -->
    <div class="loading-bar" id="loadingBar"></div>

    <!-- الخلفية المتحركة -->
    <div class="animated-background">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="main-container">
        <div class="welcome-container">
            <!-- قسم الشعار والعنوان -->
            <div class="logo-section fade-in">
                <div class="logo-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h1 class="main-title">نظام إدارة الفواتير</h1>
                <p class="subtitle">أسهل طريقة لإدارة فواتيرك ومتابعة أعمالك بكفاءة واحترافية</p>
            </div>

            <!-- مميزات النظام -->
            <div class="features-grid fade-in">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>تقارير مفصلة</h3>
                    <p>احصل على تقارير شاملة ومفصلة عن أعمالك ومبيعاتك وأرباحك</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>إدارة العملاء</h3>
                    <p>نظم بيانات عملائك واحتفظ بسجل كامل لجميع التعاملات</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3>فواتير احترافية</h3>
                    <p>أنشئ فواتير احترافية وجميلة بتصاميم متنوعة وقابلة للتخصيص</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>سهولة الاستخدام</h3>
                    <p>واجهة بسيطة وسهلة الاستخدام على جميع الأجهزة والشاشات</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>أمان عالي</h3>
                    <p>حماية متقدمة لبياناتك مع النسخ الاحتياطي التلقائي</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>تخزين سحابي</h3>
                    <p>بياناتك محفوظة في السحابة ومتاحة من أي مكان وفي أي وقت</p>
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="stats-section fade-in">
                <div class="stat-item">
                    <div class="stat-number" data-target="1250">0</div>
                    <div class="stat-label">عميل راضي</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="15000">0</div>
                    <div class="stat-label">فاتورة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="99">0</div>
                    <div class="stat-label">% رضا العملاء</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="24">0</div>
                    <div class="stat-label">ساعة دعم</div>
                </div>
            </div>

            <!-- أزرار العمل -->
            <div class="action-buttons fade-in">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>الداشبورد</span>
                    </a>
                @else
                    <a href="#" class="btn btn-primary" id="loginBtn" onclick="goToLogin(event)">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    @if (Route::has('register'))
                        <a href="#" class="btn btn-secondary" id="registerBtn" onclick="goToRegister(event)">
                            <i class="fas fa-user-plus"></i>
                            <span>إنشاء حساب جديد</span>
                        </a>
                    @endif
                @endauth
            </div>

            <!-- معلومات إضافية -->
            <div class="info-section fade-in">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4>ابدأ الآن مجاناً</h4>
                    <p>جرب النظام لمدة 30 يوم مجاناً بدون أي التزامات</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>دعم فني متميز</h4>
                    <p>فريق دعم متاح على مدار الساعة لمساعدتك</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسائل المنبثقة -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- معلومات التذييل -->
    <footer class="footer fade-in">
        <div class="footer-content">
            <p>&copy; 2025 نظام إدارة الفواتير - جميع الحقوق محفوظة</p>
            <div class="footer-links">
                <a href="#" class="footer-link">الشروط والأحكام</a>
                <a href="#" class="footer-link">سياسة الخصوصية</a>
                <a href="#" class="footer-link">اتصل بنا</a>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/welcome.js') }}"></script>

    <!-- دوال التنقل المباشرة -->
    <script>
        function goToLogin(event) {
            event.preventDefault();
            console.log('جاري الانتقال إلى صفحة تسجيل الدخول...');
            window.location.href = '/invoice/public/login';
        }

        function goToRegister(event) {
            event.preventDefault();
            console.log('جاري الانتقال إلى صفحة التسجيل...');
            window.location.href = '/invoice/public/register';
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام الفواتير</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="header">
                <div class="logo">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h1>نظام الفواتير</h1>
                </div>
                <h2>تسجيل الدخول</h2>
                <p>مرحباً بعودتك! يرجى إدخال بياناتك للدخول</p>
            </div>

            <!-- رسائل الجلسة -->
            @if (session('status'))
                <div class="auth-session-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- البريد الإلكتروني -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        البريد الإلكتروني
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input @error('email') error @enderror"
                        placeholder="example@domain.com"
                        required
                        autofocus
                        autocomplete="username"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <div class="error-message show">
                            {{ $message === 'These credentials do not match our records.' ? 'بيانات الدخول غير صحيحة' : $message }}
                        </div>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        كلمة المرور
                    </label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input @error('password') error @enderror"
                            placeholder="أدخل كلمة المرور"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-message show">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- تذكرني ونسيت كلمة المرور -->
                <div class="form-options">
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="remember" name="remember">
                            <span class="checkmark"></span>
                            تذكرني
                        </label>
                    </div>
                    <div class="forgot-password">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                        @endif
                    </div>
                </div>

                <!-- زر تسجيل الدخول -->
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>

                <!-- رابط التسجيل -->
                <div class="auth-link">
                    <p>ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // عرض/إخفاء كلمة المرور
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // إدارة رسائل الأخطاء
        document.addEventListener('DOMContentLoaded', function() {
            // إخفاء رسائل الأخطاء عند التركيز على الحقل
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    const errorMessage = this.nextElementSibling;
                    if (errorMessage && errorMessage.classList.contains('error-message')) {
                        errorMessage.classList.remove('show');
                        this.classList.remove('error');
                    }
                });
            });
        });
    </script>
</body>
</html>

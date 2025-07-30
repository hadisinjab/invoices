<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التسجيل - نظام الفواتير</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="header">
                <div class="logo">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h1>نظام الفواتير</h1>
                </div>
                <h2>إنشاء حساب جديد</h2>
                <p>مرحباً بك! قم بإنشاء حسابك للبدء</p>
            </div>

            @if (session('status'))
                <div class="success-message show">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="register-form">
                @csrf

                <!-- الاسم -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i>
                        الاسم الكامل
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input @error('name') error @enderror"
                        placeholder="أدخل اسمك الكامل"
                        required
                        autofocus
                        autocomplete="name"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <div class="error-message show">{{ $message }}</div>
                    @enderror
                </div>

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
                        autocomplete="username"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <div class="error-message show">{{ $message }}</div>
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
                            placeholder="أدخل كلمة مرور قوية"
                            required
                            autocomplete="new-password"
                            minlength="8"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    @error('password')
                        <div class="error-message show">{{ $message }}</div>
                    @enderror
                </div>

                <!-- تأكيد كلمة المرور -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock-check"></i>
                        تأكيد كلمة المرور
                    </label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="أعد إدخال كلمة المرور"
                            required
                            autocomplete="new-password"
                        >
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- شروط الاستخدام -->
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span class="checkmark"></span>
                        أوافق على <a href="/terms" target="_blank">شروط الاستخدام</a> و <a href="/privacy" target="_blank">سياسة الخصوصية</a>
                    </label>
                </div>

                <!-- زر التسجيل -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-user-plus"></i>
                    إنشاء الحساب
                    <div class="loading-spinner" id="loadingSpinner"></div>
                </button>

                <!-- رابط تسجيل الدخول -->
                <div class="login-link">
                    <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>

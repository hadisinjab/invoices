<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعادة كلمة المرور - نظام الفواتير</title>
    <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
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
                <h2>نسيت كلمة المرور</h2>
                <p>أدخل بريدك الإلكتروني وكلمة المرور الجديدة</p>
            </div>

            <!-- رسائل الجلسة -->
            @if (session('status'))
                <div class="auth-session-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form" id="resetForm">
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
                        autocomplete="email"
                        value="{{ old('email') }}"
                    >
                    <div id="email-error" class="error-message @error('email') show @enderror">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- زر إرسال رمز التحقق -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span class="btn-text">إرسال رمز التحقق</span>
                    <div class="spinner" id="loadingSpinner" style="display: none;"></div>
                </button>

                <!-- روابط إضافية -->
                <div class="auth-links">
                    <p>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-right"></i>
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                    @if (Route::has('register'))
                        <p>
                            ليس لديك حساب؟
                            <a href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i>
                                إنشاء حساب جديد
                            </a>
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // عناصر DOM
            const resetForm = document.getElementById('resetForm');
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // التحقق من صحة النموذج قبل الإرسال
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    // إعادة تعيين رسائل الخطأ
                    resetErrorMessages();

                    // التحقق من الصحة على الجانب العميل
                    const isValid = validateForm();

                    if (isValid) {
                        // عرض حالة التحميل
                        submitBtn.classList.add('loading');
                        loadingSpinner.style.display = 'block';
                        submitBtn.disabled = true;
                    } else {
                        e.preventDefault();
                    }
                });
            }

            // التحقق من صحة الحقول
            function validateForm() {
                let isValid = true;

                // التحقق من البريد الإلكتروني
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailInput.value.trim() === '') {
                    showError(emailInput, 'email-error', 'الرجاء إدخال البريد الإلكتروني');
                    isValid = false;
                } else if (!emailRegex.test(emailInput.value.trim())) {
                    showError(emailInput, 'email-error', 'البريد الإلكتروني غير صالح');
                    isValid = false;
                }

                return isValid;
            }

            // عرض رسالة الخطأ
            function showError(input, errorId, message) {
                input.classList.add('error');
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                }
            }

            // إعادة تعيين رسائل الخطأ
            function resetErrorMessages() {
                const errorMessages = document.querySelectorAll('.error-message');
                errorMessages.forEach(msg => {
                    msg.classList.remove('show');
                });

                const inputs = document.querySelectorAll('.form-input');
                inputs.forEach(input => {
                    input.classList.remove('error');
                });
            }

            // التحقق من الصحة أثناء الكتابة
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value.trim() === '') {
                        showError(this, 'email-error', 'الرجاء إدخال البريد الإلكتروني');
                    } else if (!emailRegex.test(this.value.trim())) {
                        showError(this, 'email-error', 'البريد الإلكتروني غير صالح');
                    } else {
                        this.classList.remove('error');
                        const errorElement = document.getElementById('email-error');
                        if (errorElement) {
                            errorElement.classList.remove('show');
                        }
                    }
                });
            }


        });
    </script>
</body>
</html>

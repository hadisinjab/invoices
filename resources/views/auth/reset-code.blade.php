<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال كود التحقق - نظام الفواتير</title>
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
                <h2>إدخال كود التحقق</h2>
                <p>أدخل الكود المكون من 6 أرقام المرسل إلى بريدك الإلكتروني</p>
            </div>

            <!-- رسائل الجلسة -->
            @if (session('status'))
                <div class="auth-session-status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="auth-form" id="resetForm">
                @csrf

                <!-- البريد الإلكتروني (مخفي) -->
                <input type="hidden" name="email" value="{{ session('email') }}">

                <!-- كود التحقق -->
                <div class="form-group">
                    <label for="code" class="form-label">
                        <i class="fas fa-key"></i>
                        كود التحقق
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-input @error('code') error @enderror"
                        placeholder="000000"
                        required
                        autofocus
                        maxlength="6"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                    >
                    <div id="code-error" class="error-message @error('code') show @enderror">
                        @error('code')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- كلمة المرور الجديدة -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        كلمة المرور الجديدة
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input @error('password') error @enderror"
                        placeholder="أدخل كلمة المرور الجديدة"
                        required
                        autocomplete="new-password"
                    >
                    <div id="password-error" class="error-message @error('password') show @enderror">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- تأكيد كلمة المرور الجديدة -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i>
                        تأكيد كلمة المرور الجديدة
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input @error('password_confirmation') error @enderror"
                        placeholder="أعد إدخال كلمة المرور الجديدة"
                        required
                        autocomplete="new-password"
                    >
                    <div id="password-confirmation-error" class="error-message @error('password_confirmation') show @enderror">
                        @error('password_confirmation')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- زر تغيير كلمة المرور -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-check"></i>
                    <span class="btn-text">تغيير كلمة المرور</span>
                    <div class="spinner" id="loadingSpinner" style="display: none;"></div>
                </button>

                <!-- روابط إضافية -->
                <div class="auth-links">
                    <p>
                        <a href="{{ route('password.request') }}">
                            <i class="fas fa-arrow-right"></i>
                            إعادة إرسال الكود
                        </a>
                    </p>
                    <p>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-right"></i>
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('code');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const resetForm = document.getElementById('resetForm');
            const submitBtn = document.getElementById('submitBtn');

            // تنسيق كود التحقق (إضافة مسافات)
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    // إزالة أي شيء غير الأرقام
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // التأكد من أن الطول لا يتجاوز 6 أرقام
                    if (this.value.length > 6) {
                        this.value = this.value.slice(0, 6);
                    }
                });
            }

            // التحقق من صحة النموذج
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    resetErrorMessages();

                    const isValid = validateForm();

                    if (isValid) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    } else {
                        e.preventDefault();
                    }
                });
            }

            function validateForm() {
                let isValid = true;

                // التحقق من كود التحقق
                if (!codeInput.value || codeInput.value.length !== 6) {
                    showError(codeInput, 'code-error', 'يرجى إدخال كود التحقق المكون من 6 أرقام');
                    isValid = false;
                }

                // التحقق من كلمة المرور
                if (!passwordInput.value || passwordInput.value.length < 6) {
                    showError(passwordInput, 'password-error', 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
                    isValid = false;
                }

                // التحقق من تأكيد كلمة المرور
                if (!passwordConfirmationInput.value) {
                    showError(passwordConfirmationInput, 'password-confirmation-error', 'يرجى تأكيد كلمة المرور');
                    isValid = false;
                } else if (passwordInput.value !== passwordConfirmationInput.value) {
                    showError(passwordConfirmationInput, 'password-confirmation-error', 'كلمة المرور وتأكيدها غير متطابقين');
                    isValid = false;
                }

                return isValid;
            }

            function showError(input, errorId, message) {
                input.classList.add('error');
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                }
            }

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
        });
    </script>
</body>
</html>

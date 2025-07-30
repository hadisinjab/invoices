@extends('layouts.app')

@section('title', 'تأكيد الكود')
@section('content')

<!-- شريط التحميل -->
<div class="loading-bar" id="loadingBar"></div>

<!-- الحاوية الرئيسية -->
<div class="verify-code-container">
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

    <!-- البطاقة الرئيسية -->
    <div class="verify-card">
        <!-- رأس البطاقة -->
        <div class="card-header">
            <div class="header-icon">
                <div class="icon-container">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <h1 class="card-title">تأكيد الكود</h1>
            <p class="card-subtitle">أدخل الكود المرسل إلى بريدك الإلكتروني</p>
        </div>

        <!-- محتوى البطاقة -->
        <div class="card-content">
            @if (session('status'))
                <div class="success-alert">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-content">
                        <h4>تم الإرسال بنجاح!</h4>
                        <p>{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-alert">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert-content">
                        <h4>خطأ في التحقق</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- رسالة الترحيب -->
            <div class="welcome-message">
                <div class="message-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>أدخل كود التحقق</h2>
                <p>
                    لقد أرسلنا كود التحقق إلى بريدك الإلكتروني.
                    أدخل الكود المكون من 6 أرقام للتحقق من حسابك.
                </p>
            </div>

            <!-- نموذج إدخال الكود -->
            <form method="POST" action="{{ route('verification.verify') }}" class="code-form">
                @csrf

                <div class="code-input-container">
                    <label for="code" class="code-label">كود التحقق</label>
                    <div class="code-inputs">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="0" autocomplete="off">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="1" autocomplete="off">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="2" autocomplete="off">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="3" autocomplete="off">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="4" autocomplete="off">
                        <input type="text" name="code[]" maxlength="1" class="code-input" data-index="5" autocomplete="off">
                    </div>
                    <div class="code-error" id="codeError"></div>
                </div>

                <button type="submit" class="verify-button" id="verifyButton" disabled>
                    <div class="button-content">
                        <i class="fas fa-check"></i>
                        <span>تأكيد الكود</span>
                    </div>
                    <div class="button-ripple"></div>
                </button>
            </form>

            <!-- خيارات إضافية -->
            <div class="additional-options">
                <div class="resend-section">
                    <p>لم تستلم الكود؟</p>
                    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                        @csrf
                        <button type="submit" class="resend-link" id="resendButton">
                            <i class="fas fa-paper-plane"></i>
                            <span>إرسال كود جديد</span>
                        </button>
                    </form>
                </div>

                <div class="timer-section" id="timerSection" style="display: none;">
                    <p>يمكنك إعادة الإرسال خلال:</p>
                    <div class="timer" id="timer">02:00</div>
                </div>
            </div>

            <!-- رابط تسجيل الخروج -->
            <div class="logout-section">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS المخصص -->
<link rel="stylesheet" href="{{ asset('css/email-verification.css') }}">

<!-- JavaScript للتفاعلات -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحريك شريط التحميل
    const loadingBar = document.getElementById('loadingBar');
    if (loadingBar) {
        loadingBar.classList.add('active');
        setTimeout(() => {
            loadingBar.classList.remove('active');
        }, 1000);
    }

    // إدارة حقول إدخال الكود
    const codeInputs = document.querySelectorAll('.code-input');
    const verifyButton = document.getElementById('verifyButton');
    const codeError = document.getElementById('codeError');
    const resendButton = document.getElementById('resendButton');
    const timerSection = document.getElementById('timerSection');
    const timer = document.getElementById('timer');

    let countdown = 120; // 2 دقائق
    let countdownInterval;

    // التحقق من اكتمال الكود
    function checkCodeComplete() {
        const code = Array.from(codeInputs).map(input => input.value).join('');
        const isComplete = code.length === 6 && /^\d{6}$/.test(code);

        verifyButton.disabled = !isComplete;

        if (isComplete) {
            verifyButton.style.background = 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)';
        } else {
            verifyButton.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }
    }

    // إدارة إدخال الكود
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            // السماح بالأرقام فقط
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // إزالة الأخطاء
            input.classList.remove('error');
            codeError.textContent = '';

            if (value.length === 1) {
                input.classList.add('filled');

                // الانتقال للحقل التالي
                if (index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            } else {
                input.classList.remove('filled');
            }

            checkCodeComplete();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 6);

            if (/^\d{6}$/.test(pastedData)) {
                codeInputs.forEach((input, i) => {
                    input.value = pastedData[i] || '';
                    input.classList.toggle('filled', pastedData[i] !== '');
                });
                checkCodeComplete();
            }
        });
    });

    // إدارة إعادة الإرسال
    resendButton.addEventListener('click', function(e) {
        e.preventDefault();

        // بدء العد التنازلي
        startCountdown();

        // إرسال الطلب
        const form = this.closest('form');
        form.submit();
    });

    function startCountdown() {
        resendButton.style.display = 'none';
        timerSection.style.display = 'block';

        countdownInterval = setInterval(() => {
            countdown--;
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;

            timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                timerSection.style.display = 'none';
                resendButton.style.display = 'inline-flex';
                countdown = 120;
            }
        }, 1000);
    }

    // تأثير النقر على الأزرار
    const buttons = document.querySelectorAll('.verify-button, .resend-link, .logout-button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.disabled) return;

            const ripple = document.createElement('div');
            ripple.classList.add('ripple');
            this.appendChild(ripple);

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // التركيز على الحقل الأول
    codeInputs[0].focus();
});
</script>

@endsection

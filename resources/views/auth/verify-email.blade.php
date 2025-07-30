@extends('layouts.app')

@section('title', 'تأكيد البريد الإلكتروني')
@section('content')

<!-- شريط التحميل -->
<div class="loading-bar" id="loadingBar"></div>

<!-- الحاوية الرئيسية -->
<div class="verify-email-container">
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
                    <i class="fas fa-envelope-open-text"></i>
                </div>
            </div>
            <h1 class="card-title">تأكيد البريد الإلكتروني</h1>
            <p class="card-subtitle">خطوة أخيرة لإكمال التسجيل</p>
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

            <!-- رسالة الترحيب -->
            <div class="welcome-message">
                <div class="message-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h2>تحقق من بريدك الإلكتروني</h2>
                <p>
                    شكراً لك على التسجيل! قبل البدء، هل يمكنك التحقق من بريدك الإلكتروني
                    بالنقر على الرابط الذي أرسلناه لك؟ إذا لم تستلم البريد الإلكتروني،
                    سنرسل لك واحداً آخر.
                </p>
            </div>

            <!-- ملاحظة مهمة -->
            <div class="info-note">
                <div class="note-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="note-content">
                    <h4>ملاحظة مهمة</h4>
                    <p>إذا لم تجد رسالة التحقق في صندوق الوارد، تحقق من مجلد الرسائل غير المرغوب فيها (Spam).</p>
                </div>
            </div>

            <!-- نموذج إعادة الإرسال -->
            <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                @csrf
                <button type="submit" class="resend-button">
                    <div class="button-content">
                        <i class="fas fa-paper-plane"></i>
                        <span>إرسال رابط التحقق مرة أخرى</span>
                    </div>
                    <div class="button-ripple"></div>
                </button>
            </form>

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

    // تأثير النقر على الأزرار
    const buttons = document.querySelectorAll('.resend-button, .logout-button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
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
});
</script>

@endsection

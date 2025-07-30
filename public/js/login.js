document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const rememberCheckbox = document.getElementById('remember');

    // عرض/إخفاء كلمة المرور
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // تغيير الأيقونة
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });

    // التحقق من صحة النموذج قبل الإرسال
    loginForm.addEventListener('submit', function(e) {
        // لا نمنع الإرسال الافتراضي هنا حتى يعمل الباك اند
        // e.preventDefault();

        // إعادة تعيين رسائل الخطأ
        resetErrorMessages();

        // التحقق من الصحة على الجانب العميل
        const isValid = validateForm();

        if (isValid) {
            // عرض حالة التحميل
            submitBtn.classList.add('loading');
            loadingSpinner.style.display = 'block';
            submitBtn.disabled = true;
        }
    });

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

        // التحقق من كلمة المرور
        if (passwordInput.value === '') {
            showError(passwordInput, 'password-error', 'الرجاء إدخال كلمة المرور');
            isValid = false;
        }

        return isValid;
    }

    // عرض رسالة الخطأ
    function showError(input, errorId, message) {
        input.classList.add('error');
        const errorElement = document.getElementById(errorId);
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }

    // إعادة تعيين رسائل الخطأ
    function resetErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message:not(.show)');
        errorMessages.forEach(msg => {
            msg.classList.remove('show');
        });

        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.classList.remove('error');
        });
    }

    // التحقق من الصحة أثناء الكتابة
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value.trim() === '') {
            showError(this, 'email-error', 'الرجاء إدخال البريد الإلكتروني');
        } else if (!emailRegex.test(this.value.trim())) {
            showError(this, 'email-error', 'البريد الإلكتروني غير صالح');
        } else {
            this.classList.remove('error');
            document.getElementById('email-error').classList.remove('show');
        }
    });

    passwordInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'password-error', 'الرجاء إدخال كلمة المرور');
        } else {
            this.classList.remove('error');
            document.getElementById('password-error').classList.remove('show');
        }
    });
});

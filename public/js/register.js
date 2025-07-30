document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM
    const registerForm = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const termsCheckbox = document.getElementById('terms');

    // عرض/إخفاء كلمة المرور
    togglePasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, this);
    });

    toggleConfirmPasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(confirmPasswordInput, this);
    });

    function togglePasswordVisibility(inputField, button) {
        const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
        inputField.setAttribute('type', type);

        // تغيير الأيقونة
        const icon = button.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    // تحقق من قوة كلمة المرور
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });

    function checkPasswordStrength(password) {
        // إعادة تعيين شريط القوة
        passwordStrength.className = 'password-strength';

        if (password.length === 0) {
            return;
        }

        // حساب القوة بناءً على الطبيعة والتعقيد
        let strength = 0;

        // طول كلمة المرور
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;

        // أحرف متنوعة
        if (/[A-Z]/.test(password)) strength += 1; // أحرف كبيرة
        if (/[a-z]/.test(password)) strength += 1; // أحرف صغيرة
        if (/[0-9]/.test(password)) strength += 1; // أرقام
        if (/[^A-Za-z0-9]/.test(password)) strength += 1; // رموز خاصة

        // تحديد مستوى القوة
        if (strength <= 2) {
            passwordStrength.className = 'password-strength weak';
        } else if (strength <= 4) {
            passwordStrength.className = 'password-strength medium';
        } else {
            passwordStrength.className = 'password-strength strong';
        }
    }

    // التحقق من صحة النموذج قبل الإرسال
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // إعادة تعيين رسائل الخطأ
        resetErrorMessages();

        // التحقق من الصحة
        const isValid = validateForm();

        if (isValid) {
            // عرض حالة التحميل
            submitBtn.classList.add('loading');
            loadingSpinner.style.display = 'block';

            // هنا يمكنك إضافة كود إرسال النموذج عبر AJAX أو الانتقال للخادم
            // لمثالنا سنقوم بمحاكاة إرسال النموذج
            registerForm.submit(); // إرسال النموذج فعلياً
        }
    });

    // التحقق من صحة الحقول
    function validateForm() {
        let isValid = true;

        // التحقق من الاسم
        if (nameInput.value.trim() === '') {
            showError(nameInput, 'name-error', 'الرجاء إدخال الاسم الكامل');
            isValid = false;
        } else if (nameInput.value.trim().length < 3) {
            showError(nameInput, 'name-error', 'الاسم يجب أن يكون على الأقل 3 أحرف');
            isValid = false;
        }

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
        } else if (passwordInput.value.length < 8) {
            showError(passwordInput, 'password-error', 'كلمة المرور يجب أن تكون على الأقل 8 أحرف');
            isValid = false;
        }

        // التحقق من تطابق كلمة المرور
        if (confirmPasswordInput.value === '') {
            showError(confirmPasswordInput, 'password-confirmation-error', 'الرجاء تأكيد كلمة المرور');
            isValid = false;
        } else if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, 'password-confirmation-error', 'كلمة المرور غير متطابقة');
            isValid = false;
        }

        // التحقق من الموافقة على الشروط
        if (!termsCheckbox.checked) {
            const termsError = document.createElement('div');
            termsError.className = 'error-message show';
            termsError.id = 'terms-error';
            termsError.textContent = 'يجب الموافقة على الشروط والأحكام';

            const checkboxGroup = termsCheckbox.closest('.checkbox-group');
            if (!checkboxGroup.querySelector('#terms-error')) {
                checkboxGroup.appendChild(termsError);
            }

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
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => {
            msg.classList.remove('show');
        });

        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.classList.remove('error');
        });

        const termsError = document.getElementById('terms-error');
        if (termsError) {
            termsError.remove();
        }
    }

    // عرض رسالة النجاح (لأغراض العرض فقط)
    function showSuccessMessage() {
        const successMessage = document.createElement('div');
        successMessage.className = 'success-message show';
        successMessage.innerHTML = `
            <i class="fas fa-check-circle"></i>
            تم إنشاء الحساب بنجاح! جاري توجيهك إلى لوحة التحكم...
        `;

        const header = document.querySelector('.header');
        header.insertAdjacentElement('afterend', successMessage);

        // في الواقع، هنا يمكنك توجيه المستخدم إلى صفحة أخرى أو إجراء تسجيل الدخول تلقائيًا
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 3000);
    }

    // التحقق من الصحة أثناء الكتابة
    nameInput.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            showError(this, 'name-error', 'الرجاء إدخال الاسم الكامل');
        } else if (this.value.trim().length < 3) {
            showError(this, 'name-error', 'الاسم يجب أن يكون على الأقل 3 أحرف');
        } else {
            this.classList.remove('error');
            document.getElementById('name-error').classList.remove('show');
        }
    });

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
        } else if (this.value.length < 8) {
            showError(this, 'password-error', 'كلمة المرور يجب أن تكون على الأقل 8 أحرف');
        } else {
            this.classList.remove('error');
            document.getElementById('password-error').classList.remove('show');
        }
    });

    confirmPasswordInput.addEventListener('blur', function() {
        if (this.value === '') {
            showError(this, 'password-confirmation-error', 'الرجاء تأكيد كلمة المرور');
        } else if (passwordInput.value !== this.value) {
            showError(this, 'password-confirmation-error', 'كلمة المرور غير متطابقة');
        } else {
            this.classList.remove('error');
            document.getElementById('password-confirmation-error').classList.remove('show');
        }
    });

    termsCheckbox.addEventListener('change', function() {
        const termsError = document.getElementById('terms-error');
        if (termsError) {
            termsError.remove();
        }
    });
});

// نظام إضافة العميل - JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // المتغيرات العامة
    let currentStep = 1;
    const totalSteps = 3;
    let formData = {};

    // العناصر
    const form = document.getElementById('clientForm');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const loadingBar = document.getElementById('loadingBar');
    const notification = document.getElementById('notification');
    const progressBar = document.querySelector('.progress-bar');

    // قواعد التحقق
    const validationRules = {
        name: {
            required: true,
            minLength: 2,
            maxLength: 50,
            pattern: /^[\u0600-\u06FF\s\w\-\.]+$/,
            message: 'الاسم مطلوب ويجب أن يكون بين 2-50 حرف'
        },
        email: {
            required: false,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'صيغة البريد الإلكتروني غير صحيحة'
        },
        phone: {
            required: false,
            pattern: /^[\+]?[\d\s\-\(\)]+$/,
            message: 'رقم الهاتف غير صحيح'
        },
        company: {
            required: false,
            maxLength: 100,
            message: 'اسم الشركة يجب أن يكون أقل من 100 حرف'
        },
        address: {
            required: false,
            maxLength: 200,
            message: 'العنوان يجب أن يكون أقل من 200 حرف'
        },
        city: {
            required: false,
            maxLength: 50,
            message: 'اسم المدينة يجب أن يكون أقل من 50 حرف'
        },
        country: {
            required: false
        },
        postal_code: {
            required: false,
            pattern: /^[\d\-\s]+$/,
            message: 'الرمز البريدي يجب أن يحتوي على أرقام فقط'
        }
    };

    // بدء التطبيق
    init();

    function init() {
        setupEventListeners();
        updateStepDisplay();
        setupFormValidation();
    }

    // إعداد مستمعي الأحداث
    function setupEventListeners() {
        // أزرار التنقل
        nextBtn.addEventListener('click', handleNextStep);
        prevBtn.addEventListener('click', handlePrevStep);

        // إرسال النموذج
        form.addEventListener('submit', handleFormSubmit);

        // التحقق من صحة البيانات أثناء الكتابة
        const inputs = form.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('input', () => validateField(input));
            input.addEventListener('blur', () => validateField(input));
        });

        // إغلاق الإشعارات
        const notificationClose = document.querySelector('.notification-close');
        if (notificationClose) {
            notificationClose.addEventListener('click', hideNotification);
        }

        // التنقل بلوحة المفاتيح
        document.addEventListener('keydown', handleKeyboardNavigation);
    }

    // إعداد التحقق من صحة النموذج
    function setupFormValidation() {
        // منع إرسال النموذج عند الضغط على Enter
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'submit') {
                e.preventDefault();
                if (currentStep < totalSteps) {
                    handleNextStep();
                }
            }
        });
    }

    // التعامل مع الخطوة التالية
    function handleNextStep() {
        if (validateCurrentStep()) {
            collectStepData();

            if (currentStep < totalSteps) {
                currentStep++;
                updateStepDisplay();

                if (currentStep === 3) {
                    updateReviewSection();
                }
            }
        }
    }

    // التعامل مع الخطوة السابقة
    function handlePrevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
        }
    }

    // تحديث عرض الخطوة
    function updateStepDisplay() {
        // تحديث شريط التقدم
        progressBar.className = `progress-bar step-${currentStep}`;

        // تحديث خطوات التقدم
        const steps = document.querySelectorAll('.progress-step');
        steps.forEach((step, index) => {
            const stepNumber = index + 1;
            step.classList.remove('active', 'completed');

            if (stepNumber < currentStep) {
                step.classList.add('completed');
            } else if (stepNumber === currentStep) {
                step.classList.add('active');
            }
        });

        // تحديث خطوات النموذج
        const formSteps = document.querySelectorAll('.form-step');
        formSteps.forEach((step, index) => {
            step.classList.remove('active');
            if (index + 1 === currentStep) {
                step.classList.add('active');
            }
        });

        // تحديث الأزرار
        updateButtons();

        // تأثير التحميل
        showLoadingBar();
        setTimeout(hideLoadingBar, 300);
    }

    // تحديث الأزرار
    function updateButtons() {
        prevBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
        nextBtn.style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        submitBtn.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
    }

    // التحقق من صحة الخطوة الحالية
    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        const inputs = currentStepElement.querySelectorAll('.form-input');
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            showNotification('يرجى تصحيح الأخطاء قبل المتابعة', 'error');
        }

        return isValid;
    }

    // التحقق من صحة حقل واحد
    function validateField(input) {
        const fieldName = input.name;
        const value = input.value.trim();
        const rules = validationRules[fieldName];
        const errorElement = document.getElementById(`${fieldName}-error`);

        if (!rules) return true;

        let isValid = true;
        let errorMessage = '';

        // التحقق من الحقول المطلوبة
        if (rules.required && !value) {
            isValid = false;
            errorMessage = rules.message || 'هذا الحقل مطلوب';
        }

        // التحقق من الحد الأدنى للطول
        if (isValid && rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = `يجب أن يكون ${rules.minLength} أحرف على الأقل`;
        }

        // التحقق من الحد الأقصى للطول
        if (isValid && rules.maxLength && value.length > rules.maxLength) {
            isValid = false;
            errorMessage = `يجب أن يكون ${rules.maxLength} حرف كحد أقصى`;
        }

        // التحقق من النمط
        if (isValid && rules.pattern && value && !rules.pattern.test(value)) {
            isValid = false;
            errorMessage = rules.message || 'صيغة البيانات غير صحيحة';
        }

        // عرض رسالة الخطأ
        if (errorElement) {
            errorElement.textContent = errorMessage;
            if (isValid) {
                errorElement.classList.remove('show');
                input.classList.remove('shake-effect');
            } else {
                errorElement.classList.add('show');
                input.classList.add('shake-effect');
                setTimeout(() => input.classList.remove('shake-effect'), 500);
            }
        }

        return isValid;
    }

    // جمع بيانات الخطوة
    function collectStepData() {
        const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        const inputs = currentStepElement.querySelectorAll('.form-input');

        inputs.forEach(input => {
            formData[input.name] = input.value.trim();
        });
    }

    // تحديث قسم المراجعة
    function updateReviewSection() {
        const reviewFields = [
            'name', 'email', 'phone', 'company',
            'address', 'city', 'country', 'postal_code'
        ];

        reviewFields.forEach(field => {
            const reviewElement = document.getElementById(`review-${field}`);
            if (reviewElement) {
                const value = formData[field] || document.getElementById(field)?.value || '-';
                reviewElement.textContent = value || '-';
            }
        });
    }

    // التعامل مع إرسال النموذج
    function handleFormSubmit(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // إضافة تأثير التحميل
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        showLoadingBar();

        // محاكاة إرسال البيانات
        setTimeout(() => {
            // هنا يمكنك إضافة كود إرسال البيانات الفعلي
            console.log('Form Data:', formData);

            // إرسال النموذج فعلياً
            form.submit();

            showNotification('تم حفظ العميل بنجاح!', 'success');

            // إزالة تأثير التحميل
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            hideLoadingBar();
        }, 2000);
    }

    // التنقل بلوحة المفاتيح
    function handleKeyboardNavigation(e) {
        if (e.key === 'ArrowRight' && currentStep > 1) {
            handlePrevStep();
        } else if (e.key === 'ArrowLeft' && currentStep < totalSteps) {
            handleNextStep();
        } else if (e.key === 'Escape') {
            // إغلاق الإشعارات أو العودة للخلف
            if (notification.classList.contains('show')) {
                hideNotification();
            }
        }
    }

    // عرض شريط التحميل
    function showLoadingBar() {
        loadingBar.classList.add('active');
    }

    // إخفاء شريط التحميل
    function hideLoadingBar() {
        loadingBar.classList.remove('active');
    }

    // عرض الإشعار
    function showNotification(message, type = 'success') {
        const notificationMessage = notification.querySelector('.notification-message');
        const notificationIcon = notification.querySelector('.notification-icon');

        // تحديد نوع الإشعار
        notification.className = `notification ${type}`;

        // تحديد الأيقونة
        let iconClass = 'fas fa-check-circle';
        if (type === 'error') iconClass = 'fas fa-exclamation-circle';
        if (type === 'warning') iconClass = 'fas fa-exclamation-triangle';

        notificationIcon.className = `notification-icon ${iconClass}`;
        notificationMessage.textContent = message;

        // عرض الإشعار
        notification.classList.add('show');

        // إخفاء الإشعار تلقائياً بعد 5 ثوانٍ
        setTimeout(() => {
            hideNotification();
        }, 5000);
    }

    // إخفاء الإشعار
    function hideNotification() {
        notification.classList.remove('show');
    }

    // حفظ البيانات محلياً (اختياري)
    function saveFormData() {
        const allInputs = form.querySelectorAll('.form-input');
        const data = {};

        allInputs.forEach(input => {
            data[input.name] = input.value;
        });

        // حفظ في localStorage (إذا كان متاحاً)
        try {
            localStorage.setItem('clientFormData', JSON.stringify(data));
        } catch (e) {
            console.log('localStorage not available');
        }
    }

    // استرجاع البيانات المحفوظة
    function loadSavedData() {
        try {
            const savedData = localStorage.getItem('clientFormData');
            if (savedData) {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.value = data[key];
                    }
                });
            }
        } catch (e) {
            console.log('Error loading saved data');
        }
    }

    // مسح البيانات المحفوظة
    function clearSavedData() {
        try {
            localStorage.removeItem('clientFormData');
        } catch (e) {
            console.log('localStorage not available');
        }
    }

    // تنسيق رقم الهاتف أثناء الكتابة
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('966')) {
                value = '+966 ' + value.substring(3);
            } else if (value.startsWith('0')) {
                value = '+966 ' + value.substring(1);
            }

            // تنسيق الرقم
            if (value.length > 4) {
                value = value.substring(0, 4) + ' ' + value.substring(4);
            }
            if (value.length > 7) {
                value = value.substring(0, 7) + ' ' + value.substring(7);
            }
            if (value.length > 11) {
                value = value.substring(0, 11) + ' ' + value.substring(11);
            }

            e.target.value = value.substring(0, 17); // حد أقصى 17 حرف
        });
    }

    // تنسيق البريد الإلكتروني
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toLowerCase();
        });
    }

    // حفظ البيانات عند كل تغيير
    const allInputs = document.querySelectorAll('.form-input');
    allInputs.forEach(input => {
        input.addEventListener('input', saveFormData);
    });

    // استرجاع البيانات المحفوظة عند التحميل
    loadSavedData();

    // مسح البيانات عند نجاح الإرسال
    form.addEventListener('submit', function() {
        setTimeout(clearSavedData, 1000);
    });

    // تأثيرات إضافية
    const formCard = document.querySelector('.form-card');
    if (formCard) {
        formCard.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        formCard.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }

    // تأثير النقر على الأزرار
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('pulse-effect');
            setTimeout(() => {
                this.classList.remove('pulse-effect');
            }, 600);
        });
    });

    // التحقق من الاتصال بالإنترنت
    function checkConnection() {
        if (!navigator.onLine) {
            showNotification('لا يوجد اتصال بالإنترنت', 'warning');
        }
    }

    window.addEventListener('online', () => {
        showNotification('تم استعادة الاتصال بالإنترنت', 'success');
    });

    window.addEventListener('offline', () => {
        showNotification('انقطع الاتصال بالإنترنت', 'error');
    });

    // فحص الاتصال عند التحميل
    checkConnection();
});

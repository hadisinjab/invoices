// تهيئة النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    initializeSystem();
});

// تهيئة النظام الرئيسي
function initializeSystem() {
    showLoadingBar();
    initializeAnimations();
    initializeEventListeners();
    initializeCounters();
    hideLoadingBar();
}

// إظهار شريط التحميل
function showLoadingBar() {
    const loadingBar = document.getElementById('loadingBar');
    loadingBar.classList.add('active');
}

// إخفاء شريط التحميل
function hideLoadingBar() {
    setTimeout(() => {
        const loadingBar = document.getElementById('loadingBar');
        loadingBar.classList.remove('active');
    }, 1000);
}

// تهيئة التأثيرات والأنيميشن
function initializeAnimations() {
    // تطبيق تأثير الظهور التدريجي على العناصر
    const fadeElements = document.querySelectorAll('.fade-in');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.animationDelay = `${index * 0.2}s`;
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    fadeElements.forEach(element => {
        observer.observe(element);
    });

    // تأثيرات إضافية للبطاقات
    initializeCardEffects();

    // تأثير الخلفية المتحركة
    initializeFloatingShapes();
}

// تأثيرات البطاقات
function initializeCardEffects() {
    const featureCards = document.querySelectorAll('.feature-card');

    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(102, 126, 234, 0.3)';

            // تأثير الأيقونة
            const icon = this.querySelector('.feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 10px 25px rgba(102, 126, 234, 0.1)';

            // إعادة تعيين الأيقونة
            const icon = this.querySelector('.feature-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });

        // تأثير النقر
        card.addEventListener('click', function() {
            this.style.transform = 'translateY(-5px) scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            }, 150);
        });
    });
}

// تأثير الأشكال العائمة
function initializeFloatingShapes() {
    const shapes = document.querySelectorAll('.shape');

    shapes.forEach((shape, index) => {
        // حركة عشوائية للأشكال
        setInterval(() => {
            const randomX = Math.random() * 20 - 10;
            const randomY = Math.random() * 20 - 10;
            const randomRotation = Math.random() * 360;

            shape.style.transform = `translate(${randomX}px, ${randomY}px) rotate(${randomRotation}deg)`;
        }, 3000 + index * 1000);
    });
}

// تهيئة مستمعي الأحداث
function initializeEventListeners() {
    // أزرار تسجيل الدخول والتسجيل
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');

    if (loginBtn) {
        loginBtn.addEventListener('click', handleLoginClick);
    }

    if (registerBtn) {
        registerBtn.addEventListener('click', handleRegisterClick);
    }

    // روابط التذييل
    const footerLinks = document.querySelectorAll('.footer-link');
    footerLinks.forEach(link => {
        link.addEventListener('click', handleFooterLinkClick);
    });

    // تأثيرات الأزرار
    initializeButtonEffects();

    // تأثير التمرير
    initializeScrollEffects();
}

// معالج النقر على زر تسجيل الدخول
function handleLoginClick(e) {
    e.preventDefault();
    showLoadingBar();

    // محاكاة التحميل
    setTimeout(() => {
        showToast('جاري تحويلك إلى صفحة تسجيل الدخول...', 'success');

        // التنقل إلى صفحة تسجيل الدخول في Laravel
        setTimeout(() => {
            hideLoadingBar();
            window.location.href = '/invoice/public/login';
        }, 1500);
    }, 500);
}

// معالج النقر على زر التسجيل
function handleRegisterClick(e) {
    e.preventDefault();
    showLoadingBar();

    // محاكاة التحميل
    setTimeout(() => {
        showToast('جاري تحويلك إلى صفحة إنشاء الحساب...', 'success');

        // التنقل إلى صفحة التسجيل في Laravel
        setTimeout(() => {
            hideLoadingBar();
            window.location.href = '/invoice/public/register';
        }, 1500);
    }, 500);
}

// معالج روابط التذييل
function handleFooterLinkClick(e) {
    e.preventDefault();
    const linkText = e.target.textContent;
    showToast(`جاري فتح صفحة ${linkText}...`, 'info');
}

// تأثيرات الأزرار
function initializeButtonEffects() {
    const buttons = document.querySelectorAll('.btn');

    buttons.forEach(button => {
        // تأثير الضغط
        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(-1px) scale(0.98)';
        });

        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-3px) scale(1)';
        });

        // تأثير التمرير
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });

        // تأثير التركيز
        button.addEventListener('focus', function() {
            this.style.outline = '3px solid rgba(102, 126, 234, 0.3)';
            this.style.outlineOffset = '2px';
        });

        button.addEventListener('blur', function() {
            this.style.outline = 'none';
        });
    });
}

// تأثيرات التمرير
function initializeScrollEffects() {
    let ticking = false;

    function updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;

        // تأثير المنظر المتوازي للخلفية
        const shapes = document.querySelectorAll('.shape');
        shapes.forEach((shape, index) => {
            const speed = (index + 1) * 0.1;
            shape.style.transform += ` translateY(${rate * speed}px)`;
        });

        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick);
}

// تهيئة عدادات الإحصائيات
function initializeCounters() {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200; // كلما قل الرقم، زادت السرعة

    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');

                animateCounter(counter, target, speed);
                observer.unobserve(counter);
            }
        });
    }, observerOptions);

    counters.forEach(counter => {
        observer.observe(counter);
    });
}

// تحريك العداد
function animateCounter(counter, target, speed) {
    const inc = target / speed;
    let current = 0;

    const timer = setInterval(() => {
        current += inc;

        if (current >= target) {
            counter.textContent = target.toLocaleString('ar-SA');
            clearInterval(timer);
        } else {
            counter.textContent = Math.ceil(current).toLocaleString('ar-SA');
        }
    }, 1);
}

// نظام الرسائل المنبثقة
function showToast(message, type = 'info', duration = 3000) {
    const toastContainer = document.getElementById('toastContainer');

    // إنشاء عنصر الرسالة
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    // إضافة الأيقونة المناسبة
    let icon = '';
    switch (type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
    }

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            ${icon}
            <span>${message}</span>
            <button onclick="closeToast(this)" style="background: none; border: none; color: #666; cursor: pointer; margin-right: auto;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    // إضافة الرسالة إلى الحاوي
    toastContainer.appendChild(toast);

    // إزالة الرسالة تلقائياً
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                toastContainer.removeChild(toast);
            }, 300);
        }
    }, duration);
}

// إغلاق الرسالة المنبثقة
function closeToast(button) {
    const toast = button.closest('.toast');
    toast.style.animation = 'slideOut 0.3s ease-in';
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// وظائف مساعدة للتنقل (للاستخدام المستقبلي)
function navigateToLogin() {
    showLoadingBar();
    showToast('جاري تحميل صفحة تسجيل الدخول...', 'success');

    setTimeout(() => {
        // التنقل إلى صفحة تسجيل الدخول في Laravel
        window.location.href = '/invoice/public/login';
        hideLoadingBar();
    }, 1500);
}

function navigateToRegister() {
    showLoadingBar();
    showToast('جاري تحميل صفحة إنشاء الحساب...', 'success');

    setTimeout(() => {
        // التنقل إلى صفحة التسجيل في Laravel
        window.location.href = '/invoice/public/register';
        hideLoadingBar();
    }, 1500);
}

// معالج الأخطاء العام
window.addEventListener('error', function(e) {
    console.error('خطأ في التطبيق:', e.error);
    showToast('حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.', 'error');
});

// معالج عدم الاتصال بالإنترنت
window.addEventListener('offline', function() {
    showToast('فقدان الاتصال بالإنترنت', 'warning', 5000);
});

window.addEventListener('online', function() {
    showToast('تم استعادة الاتصال بالإنترنت', 'success');
});

// تأثيرات إضافية للشعار
function initializeLogoEffects() {
    const logoIcon = document.querySelector('.logo-icon');

    if (logoIcon) {
        logoIcon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
            this.style.boxShadow = '0 10px 30px rgba(102, 126, 234, 0.3)';
        });

        logoIcon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.boxShadow = 'none';
        });

        // تأثير النقر
        logoIcon.addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 2s infinite';
            }, 100);

            showToast('مرحباً بك في نظام إدارة الفواتير! 🎉', 'success');
        });
    }
}

// تفعيل تأثيرات الشعار عند التحميل
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeLogoEffects, 1000);
});

// تأثير الكتابة المتحركة للعنوان الفرعي
function initializeTypingEffect() {
    const subtitle = document.querySelector('.subtitle');
    if (!subtitle) return;

    const text = subtitle.textContent;
    subtitle.textContent = '';
    subtitle.style.opacity = '1';

    let i = 0;
    const typing = setInterval(() => {
        if (i < text.length) {
            subtitle.textContent += text.charAt(i);
            i++;
        } else {
            clearInterval(typing);
        }
    }, 50);
}

// تأثيرات لوحة المفاتيح
document.addEventListener('keydown', function(e) {
    // اختصارات لوحة المفاتيح
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        handleLoginClick(e);
    }

    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        handleRegisterClick(e);
    }

    // الهروب لإغلاق الرسائل المنبثقة
    if (e.key === 'Escape') {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            closeToast(toast.querySelector('button'));
        });
    }
});

// تحسين الأداء - تأجيل التأثيرات غير الضرورية
function deferNonCriticalEffects() {
    setTimeout(() => {
        initializeTypingEffect();
    }, 2000);
}

// تشغيل التأثيرات المؤجلة
window.addEventListener('load', deferNonCriticalEffects);

// إضافة أنماط CSS ديناميكية للتأثيرات الإضافية
function addDynamicStyles() {
    const welcomeStyle = document.createElement('style');
    welcomeStyle.textContent = `
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(-100%);
                opacity: 0;
            }
        }

        .btn:active {
            transform: translateY(-1px) scale(0.98) !important;
        }

        .feature-card:active {
            transform: translateY(-5px) scale(0.98) !important;
        }
    `;
    document.head.appendChild(welcomeStyle);
}

// تشغيل الأنماط الديناميكية
document.addEventListener('DOMContentLoaded', addDynamicStyles);

console.log('✅ نظام إدارة الفواتير - تم تحميل الجافاسكريبت بنجاح!');

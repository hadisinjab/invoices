// Products Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Products List Page');

    // اختبار بسيط لمعرفة ما إذا كان JavaScript يعمل
    console.log('JavaScript is working!');
    console.log('Price inputs found:', document.querySelectorAll('.price-input').length);
    console.log('Save buttons found:', document.querySelectorAll('.save-btn').length);
    console.log('Price forms found:', document.querySelectorAll('.price-form').length);

    // عناصر DOM
    const searchInput = document.getElementById('product-search-input');
    const resetSearchBtn = document.getElementById('reset-search');
    const searchBtn = document.getElementById('search-btn');
    const productsTable = document.getElementById('productsTable');
    const successMessage = document.getElementById('successMessage');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const priceInputs = document.querySelectorAll('.price-input');
    const saveBtns = document.querySelectorAll('.save-btn');
    const priceForms = document.querySelectorAll('.price-form');

    console.log('DOM Elements found:', {
        searchInput: !!searchInput,
        resetSearchBtn: !!resetSearchBtn,
        productsTable: !!productsTable,
        priceInputs: priceInputs.length,
        priceForms: priceForms.length
    });

    // تهيئة الصفحة
    initializePage();

    // وظائف التهيئة
    function initializePage() {
        console.log('Initializing page...');

        try {
            // التحقق من وجود العناصر المطلوبة
            if (!searchInput) {
                console.error('Search input not found');
            }
            if (!resetSearchBtn) {
                console.error('Reset search button not found');
            }
            if (!productsTable) {
                console.error('Products table not found');
            }

            // تفعيل مراقبة تغيير الأسعار
            setupPriceInputListeners();

            // تفعيل نماذج الحفظ
            setupFormSubmissions();

            // تأثيرات الحركة
            setupAnimations();

            // تفعيل الاختصارات
            setupKeyboardShortcuts();

            // تحديث الإحصائيات عند تحميل الصفحة
            setTimeout(() => {
                updateStatistics();
            }, 1000);

            console.log('Page initialization completed');
        } catch (error) {
            console.error('Error initializing page:', error);
        }
    }

    // تعريف الدوال في النطاق العام
    window.setupPriceInputListeners = setupPriceInputListeners;
    window.setupFormSubmissions = setupFormSubmissions;

    // مراقبة تغيير الأسعار
    function setupPriceInputListeners() {
        try {
            console.log('Setting up price input listeners...');
            document.querySelectorAll('.price-input').forEach((input) => {
                console.log('Found price input:', input);
                const form = input.closest('.price-form');
                if (!form) {
                    console.error('No form found for input:', input);
                    return;
                }

                const saveBtn = form.querySelector('.save-btn');
                if (!saveBtn) {
                    console.error('No save button found for input:', input);
                    return;
                }

                const originalValue = input.dataset.originalValue || '';
                console.log('Original value:', originalValue);

                input.addEventListener('input', function() {
                    const currentValue = this.value.trim();
                    const hasChanged = currentValue !== originalValue;
                    console.log('Input changed:', currentValue, 'Original:', originalValue, 'Has changed:', hasChanged);

                    saveBtn.disabled = !(hasChanged && currentValue !== '');
                    saveBtn.classList.toggle('btn-enabled', hasChanged && currentValue !== '');
                    this.classList.toggle('changed', hasChanged && currentValue !== '');

                    console.log('Save button enabled:', !saveBtn.disabled);
                    console.log('Save button has btn-enabled class:', saveBtn.classList.contains('btn-enabled'));

                    // تحديث الإحصائيات عند تغيير السعر
                    setTimeout(() => {
                        updateStatistics();
                    }, 1000);
                });

                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                    this.select();
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Handle purchase price dropdowns
            document.querySelectorAll('.purchase-price-form').forEach(form => {
                const productId = form.dataset.productId;
                const priceInput = form.querySelector('.purchase-price-input');
                const dropdown = form.querySelector('.last-prices-dropdown');

                fetchLastPurchasePrices(productId, (prices) => {
                    if (prices.length > 0) {
                        const sum = prices.reduce((a, b) => a + parseFloat(b), 0);
                        const avg = sum / prices.length;
                        priceInput.value = avg.toFixed(2);

                        dropdown.innerHTML = '<option value="">آخر الأسعار</option>';
                        prices.forEach(price => {
                            const option = document.createElement('option');
                            option.value = price;
                            option.textContent = parseFloat(price).toFixed(2);
                            dropdown.appendChild(option);
                        });
                    } else {
                         dropdown.innerHTML = '<option value="">لا يوجد</option>';
                         dropdown.disabled = true;
                    }
                });

                dropdown.addEventListener('change', function() {
                    if (this.value) {
                        priceInput.value = this.value;
                        // Trigger input event to enable save button
                        priceInput.dispatchEvent(new Event('input'));

                        // تحديث الإحصائيات عند تغيير السعر من dropdown
                        setTimeout(() => {
                            updateStatistics();
                        }, 1000);
                    }
                });
            });
        } catch (error) {
            console.error('Error setting up price input listeners:', error);
        }
    }

    function fetchLastPurchasePrices(productId, callback) {
        try {
            fetch(`/invoice/public/api/products/${productId}/last-purchase-prices`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.prices) {
                        callback(data.prices);
                    } else {
                        callback([]);
                    }
                })
                .catch(error => {
                    console.error('Error fetching last purchase prices:', error);
                    callback([]);
                });
        } catch (error) {
            console.error('Error in fetchLastPurchasePrices:', error);
            callback([]);
        }
    }

    // تفعيل نماذج الحفظ
    function setupFormSubmissions() {
        try {
            console.log('Setting up form submissions...');
            document.querySelectorAll('.price-form').forEach((form, index) => {
                console.log('Found form:', form, 'Action:', form.action);
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submitted:', this.action);

                    const formData = new FormData(this);
                    const saveBtn = this.querySelector('.save-btn');
                    const priceInput = this.querySelector('.price-input');

                    // تفعيل حالة التحميل
                    showLoadingState(saveBtn);
                    showLoadingOverlay();

                    // إصلاح المسار إذا كان نسبياً
                    let actionUrl = this.action;
                    if (!actionUrl.startsWith('http') && !actionUrl.startsWith('/invoice/public')) {
                        actionUrl = '/invoice/public' + actionUrl;
                    }

                    // محاكاة طلب AJAX
                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoadingOverlay();
                        hideLoadingState(saveBtn);

                        if (data.success) {
                            showSuccessMessage(data.message || 'تم حفظ السعر بنجاح!');
                            addSuccessEffect(this.closest('tr'));

                            // تحديث القيمة الأصلية
                            priceInput.dataset.originalValue = priceInput.value;
                            saveBtn.disabled = true;
                            saveBtn.classList.remove('btn-enabled');
                            priceInput.classList.remove('changed');

                            // تحديث الإحصائيات
                            setTimeout(() => {
                                updateStatistics();
                            }, 1000);
                        } else {
                            showErrorMessage(data.message || 'حدث خطأ أثناء الحفظ');
                        }
                    })
                    .catch(error => {
                        hideLoadingOverlay();
                        hideLoadingState(saveBtn);
                        console.error('Error:', error);
                        console.error('Error response:', error.response);

                        if (error.response && error.response.status === 404) {
                            showErrorMessage('المسار غير موجود. تأكد من تسجيل الدخول.');
                        } else if (error.response && error.response.status === 401) {
                            showErrorMessage('يجب تسجيل الدخول أولاً.');
                            setTimeout(() => {
                                window.location.href = '/invoice/public/login';
                            }, 2000);
                        } else if (error.response && error.response.status === 422) {
                            showErrorMessage('بيانات غير صحيحة. تأكد من إدخال سعر صحيح.');
                        } else {
                            showErrorMessage('حدث خطأ في الاتصال: ' + (error.message || 'خطأ غير معروف'));
                        }
                    });
                });
            });
        } catch (error) {
            console.error('Error setting up form submissions:', error);
        }
    }

    // عرض حالة التحميل للزر
    function showLoadingState(button) {
        try {
            button.disabled = true;
            button.classList.remove('btn-enabled');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
        } catch (error) {
            console.error('Error showing loading state:', error);
        }
    }

    // إخفاء حالة التحميل للزر
    function hideLoadingState(button) {
        try {
            button.disabled = false;
            // إعادة تعيين الزر إلى حالته الأصلية
            const originalValue = button.closest('.price-form').querySelector('.price-input').dataset.originalValue;
            const currentValue = button.closest('.price-form').querySelector('.price-input').value.trim();
            const hasChanged = currentValue !== originalValue;

            button.innerHTML = '<i class="fas fa-save"></i> حفظ';
            button.disabled = !(hasChanged && currentValue !== '');
            button.classList.toggle('btn-enabled', hasChanged && currentValue !== '');
        } catch (error) {
            console.error('Error hiding loading state:', error);
        }
    }

    // عرض طبقة التحميل
    function showLoadingOverlay() {
        try {
            if (loadingOverlay) {
                loadingOverlay.classList.add('show');
            }
        } catch (error) {
            console.error('Error showing loading overlay:', error);
        }
    }

    // إخفاء طبقة التحميل
    function hideLoadingOverlay() {
        try {
            if (loadingOverlay) {
                loadingOverlay.classList.remove('show');
            }
        } catch (error) {
            console.error('Error hiding loading overlay:', error);
        }
    }

    // إظهار رسالة النجاح
    function showSuccessMessage(message = 'تم حفظ السعر بنجاح!') {
        try {
            // إنشاء رسالة نجاح إذا لم تكن موجودة
            let successMsg = document.getElementById('successMessage');
            if (!successMsg) {
                successMsg = document.createElement('div');
                successMsg.id = 'successMessage';
                successMsg.className = 'success-message';
                successMsg.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <span></span>
                `;
                document.body.appendChild(successMsg);
            }

            successMsg.querySelector('span').textContent = message;
            successMsg.classList.add('show');

            // إخفاء الرسالة بعد 3 ثوانٍ
            setTimeout(() => {
                successMsg.classList.remove('show');
            }, 3000);
        } catch (error) {
            console.error('Error showing success message:', error);
        }
    }

    // إظهار رسالة الخطأ
    function showErrorMessage(message) {
        try {
            // إنشاء رسالة خطأ إذا لم تكن موجودة
            let errorMessage = document.getElementById('errorMessage');
            if (!errorMessage) {
                errorMessage = document.createElement('div');
                errorMessage.id = 'errorMessage';
                errorMessage.className = 'error-message';
                errorMessage.innerHTML = `
                    <i class="fas fa-exclamation-circle"></i>
                    <span></span>
                `;
                document.body.appendChild(errorMessage);
            }

            errorMessage.querySelector('span').textContent = message;
            errorMessage.classList.add('show');

            // إخفاء الرسالة بعد 4 ثوانٍ
            setTimeout(() => {
                errorMessage.classList.remove('show');
            }, 4000);
        } catch (error) {
            console.error('Error showing error message:', error);
        }
    }

    // إضافة تأثير النجاح
    function addSuccessEffect(row) {
        try {
            console.log('Adding success effect to row:', row);
            row.classList.add('success-flash');

            setTimeout(() => {
                row.classList.remove('success-flash');
            }, 1500);
        } catch (error) {
            console.error('Error adding success effect:', error);
        }
    }

    // تفعيل الحركات والتأثيرات
    function setupAnimations() {
        try {
            // تأثير الظهور التدريجي للصفوف
            const rows = document.querySelectorAll('.product-row');
            rows.forEach((row, index) => {
                row.style.animationDelay = (index * 100) + 'ms';
                row.classList.add('fade-in-up');
            });

            // تأثير التمرير على الجدول
            const tableWrapper = document.querySelector('.table-wrapper');
            if (tableWrapper) {
                tableWrapper.addEventListener('scroll', function() {
                    const scrollTop = this.scrollTop;
                    const header = this.querySelector('thead');

                    if (scrollTop > 0) {
                        header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                    } else {
                        header.style.boxShadow = 'none';
                    }
                });
            }

            // تأثير الـ parallax للبطاقات
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateX = (y - centerY) / 10;
                    const rotateY = (centerX - x) / 10;

                    this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
                });
            });
        } catch (error) {
            console.error('Error setting up animations:', error);
        }
    }

    // تفعيل اختصارات لوحة المفاتيح
    function setupKeyboardShortcuts() {
        try {
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + F للبحث
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }

                // Escape لإعادة تعيين البحث
                if (e.key === 'Escape') {
                    if (searchInput && searchInput.value.trim() !== '') {
                        searchInput.value = '';
                        // filterProducts(''); // This line is removed as per the edit hint
                        if (resetSearchBtn) {
                            resetSearchBtn.style.display = 'none';
                        }
                        searchInput.focus();
                    }
                }

                // Enter لحفظ التغييرات في الحقل النشط
                if (e.key === 'Enter') {
                    const activeElement = document.activeElement;
                    if (activeElement && activeElement.classList.contains('price-input')) {
                        const form = activeElement.closest('.price-form');
                        if (form) {
                            const saveBtn = form.querySelector('.save-btn');
                            if (saveBtn && !saveBtn.disabled) {
                                saveBtn.click();
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error setting up keyboard shortcuts:', error);
        }
    }

    // تحديث الإحصائيات
    function updateStatistics() {
        console.log('Updating statistics...');

        try {
            const rows = document.querySelectorAll('.product-row');
            const visibleRows = Array.from(rows).filter(row =>
                row.style.display !== 'none' &&
                !row.classList.contains('no-results-row') &&
                !row.classList.contains('empty-state')
            );

            console.log('Visible rows count:', visibleRows.length);

            // تحديث عدد المنتجات المرئية
            const totalProductsCard = document.querySelector('.total-products .stat-number');
            if (totalProductsCard) {
                totalProductsCard.textContent = visibleRows.length;
                console.log('Updated total products:', visibleRows.length);
            } else {
                console.warn('Total products card not found');
            }

            // تحديث إجمالي الكمية للمنتجات المرئية
            const totalQuantityCard = document.querySelector('.total-quantity .stat-number');
            if (totalQuantityCard) {
                let totalQuantity = 0;
                visibleRows.forEach(row => {
                    const quantityElement = row.querySelector('.quantity-badge');
                    if (quantityElement) {
                        const quantity = parseInt(quantityElement.textContent.trim()) || 0;
                        totalQuantity += quantity;
                    }
                });
                totalQuantityCard.textContent = totalQuantity.toLocaleString('ar-SA');
                console.log('Updated total quantity:', totalQuantity);
            } else {
                console.warn('Total quantity card not found');
            }

            // تحديث إجمالي قيمة المنتجات المرئية
            const totalStockValueCard = document.querySelector('.total-stock-value .stat-number');
            if (totalStockValueCard) {
                let totalValue = 0;
                visibleRows.forEach(row => {
                    const purchasePriceInput = row.querySelector('.purchase-price-input');
                    const quantityElement = row.querySelector('.quantity-badge');
                    if (purchasePriceInput && quantityElement) {
                        const price = parseFloat(purchasePriceInput.value) || 0;
                        const quantity = parseInt(quantityElement.textContent.trim()) || 0;
                        totalValue += price * quantity;
                    }
                });
                totalStockValueCard.textContent = totalValue.toLocaleString('ar-SA', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                console.log('Updated total value:', totalValue);
            } else {
                console.warn('Total stock value card not found');
            }

            // إضافة تأثير بصري لتحديث الإحصائيات
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.classList.add('stat-updated');
                setTimeout(() => {
                    card.classList.remove('stat-updated');
                }, 300);
            });
        } catch (error) {
            console.error('Error updating statistics:', error);
        }
    }

    // تحديث الوقت
    function updateTime() {
        try {
            const now = new Date();
            const timeString = now.toLocaleTimeString('ar-SA');

            // إضافة الوقت إلى الصفحة إذا كان هناك عنصر مخصص لذلك
            const timeDisplay = document.querySelector('.time-display');
            if (timeDisplay) {
                timeDisplay.textContent = timeString;
            }
        } catch (error) {
            console.error('Error updating time:', error);
        }
    }

    // تحديث الوقت كل ثانية
    setInterval(updateTime, 1000);
    updateTime();

    // مراقبة تغيير حجم النافذة
    window.addEventListener('resize', function() {
        try {
            // إعادة تعيين التأثيرات للأجهزة المحمولة
            if (window.innerWidth <= 768) {
                document.body.classList.add('mobile-view');
            } else {
                document.body.classList.remove('mobile-view');
            }
        } catch (error) {
            console.error('Error handling window resize:', error);
        }
    });

    // تفعيل الـ Service Worker للتخزين المؤقت (اختياري)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            try {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            } catch (error) {
                console.error('Error registering service worker:', error);
            }
        });
    }

    // تأثيرات إضافية للتفاعل

    // تأثير الموجة عند النقر
    document.addEventListener('click', function(e) {
        try {
            if (e.target.classList.contains('save-btn') || e.target.closest('.save-btn')) {
                createRippleEffect(e);
            }
        } catch (error) {
            console.error('Error handling click event:', error);
        }
    });

    function createRippleEffect(event) {
        try {
            const button = event.target.closest('.save-btn');
            if (!button) return;

            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                transform: scale(0);
                left: ${x}px;
                top: ${y}px;
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;

            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        } catch (error) {
            console.error('Error creating ripple effect:', error);
        }
    }

    // إضافة CSS للتأثيرات الإضافية
    const additionalStyles = `
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .fade-in-search {
            animation: fadeInSearch 0.3s ease-out;
        }

        .success-flash {
            animation: successFlash 1.5s ease-out;
        }

        .stat-updated {
            animation: statUpdate 0.3s ease-out;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInSearch {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes successFlash {
            0% { background-color: rgba(17, 153, 142, 0.1); }
            50% { background-color: rgba(17, 153, 142, 0.3); }
            100% { background-color: transparent; }
        }

        @keyframes statUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .error-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        .error-message.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .search-focused {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            transform: scale(1.02);
        }

        .mobile-view .table-header {
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-view .search-box {
            width: 100%;
        }

        .mobile-view .products-table th,
        .mobile-view .products-table td {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
    `;

    // إضافة الأنماط إلى الصفحة
    const styleSheet = document.createElement('style');
    styleSheet.textContent = additionalStyles;
    document.head.appendChild(styleSheet);

    console.log('Products List Page JavaScript loaded successfully!');

    // استدعاء إضافي للدوال بعد تحميل الصفحة
    setTimeout(() => {
        console.log('Additional setup after page load...');
        setupPriceInputListeners();
        setupFormSubmissions();

        // اختبار مباشر لتفعيل الأزرار
        console.log('Testing button activation...');
        document.querySelectorAll('.price-input').forEach((input, index) => {
            console.log(`Testing input ${index + 1}:`, input.value, 'Original:', input.dataset.originalValue);
            const form = input.closest('.price-form');
            const saveBtn = form ? form.querySelector('.save-btn') : null;
            if (saveBtn) {
                console.log(`Save button ${index + 1} disabled:`, saveBtn.disabled);
                console.log(`Save button ${index + 1} classes:`, saveBtn.className);
            }
        });
    }, 1000);

    // دالة اختبار لتفعيل الأزرار يدوياً
    window.testButtonActivation = function() {
        console.log('Manual button activation test...');
        document.querySelectorAll('.price-input').forEach((input, index) => {
            const form = input.closest('.price-form');
            const saveBtn = form ? form.querySelector('.save-btn') : null;
            if (saveBtn) {
                // تفعيل الزر مؤقتاً للاختبار
                saveBtn.disabled = false;
                saveBtn.classList.add('btn-enabled');
                console.log(`Button ${index + 1} activated manually`);
            }
        });
    };

    // استدعاء دالة الاختبار بعد 2 ثانية
    setTimeout(() => {
        window.testButtonActivation();
    }, 2000);
});

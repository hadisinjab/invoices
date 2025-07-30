document.addEventListener('DOMContentLoaded', function() {
    // متغيرات عامة
    let currentView = 'grid';
    let currentFilter = 'all';
    let searchTimeout;
    let clientsData = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    // الحصول على العناصر الأساسية
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const clientsContainer = document.getElementById('clientsContainer');
    const emptyState = document.getElementById('emptyState');
    const successAlert = document.getElementById('successAlert');
    const successMessage = document.getElementById('successMessage');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const viewButtons = document.querySelectorAll('.view-btn');
    const deleteModal = document.getElementById('deleteModal');
    const exportBtn = document.getElementById('exportBtn');
    const importBtn = document.getElementById('importBtn');
    const addClientBtn = document.getElementById('addClientBtn');

    // إعداد CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    // تحميل البيانات من Laravel
    function loadClientsFromLaravel() {
        // تحويل العملاء الموجودين في الـ HTML إلى بيانات JS
        const clientCards = document.querySelectorAll('.client-card');
        clientsData = Array.from(clientCards).map(card => {
            const clientId = card.getAttribute('data-client-id');
            const name = card.querySelector('.client-name')?.textContent || '';
            const email = card.querySelector('.client-email')?.textContent || '';
            const status = card.querySelector('.client-status')?.classList.contains('active') ? 'active' : 'inactive';

            return {
                id: clientId,
                name: name,
                email: email,
                status: status,
                element: card
            };
        });

        updateView();
    }

    // تهيئة البحث
    function initSearch() {
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(e.target.value);
                }, 300);
            });

            // إضافة تأثير focus
            searchInput.addEventListener('focus', function() {
                this.parentElement.classList.add('search-focused');
            });

            searchInput.addEventListener('blur', function() {
                this.parentElement.classList.remove('search-focused');
            });
        }

        // زر مسح البحث
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch('');
                searchInput.focus();
            });
        }
    }

    // تنفيذ البحث
    function performSearch(query) {
        const filteredClients = clientsData.filter(client => {
            const matchesSearch = query === '' ||
                client.name.toLowerCase().includes(query.toLowerCase()) ||
                client.email.toLowerCase().includes(query.toLowerCase());

            const matchesFilter = currentFilter === 'all' || client.status === currentFilter;

            return matchesSearch && matchesFilter;
        });

        updateClientDisplay(filteredClients);
        updateClearButton(query);
    }

    // تحديث زر مسح البحث
    function updateClearButton(query) {
        if (clearSearchBtn) {
            clearSearchBtn.style.display = query ? 'block' : 'none';
        }
    }

    // تهيئة أزرار التصفية
    function initFilters() {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // إزالة الكلاس النشط من جميع الأزرار
                filterButtons.forEach(b => b.classList.remove('active'));
                // إضافة الكلاس النشط للزر المحدد
                this.classList.add('active');

                currentFilter = this.getAttribute('data-filter');
                performSearch(searchInput.value);

                // تأثير النقر
                addClickEffect(this);
            });
        });
    }

    // تهيئة أزرار العرض
    function initViewToggle() {
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                viewButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                currentView = this.getAttribute('data-view');
                updateView();

                addClickEffect(this);
            });
        });
    }

    // تحديث العرض
    function updateView() {
        if (clientsContainer) {
            clientsContainer.className = `clients-container ${currentView}-view`;
        }
    }

    // تحديث عرض العملاء
    function updateClientDisplay(clients) {
        if (!clientsContainer) return;

        // إخفاء جميع البطاقات
        clientsData.forEach(client => {
            if (client.element) {
                client.element.style.display = 'none';
            }
        });

        // إظهار البطاقات المطابقة
        clients.forEach(client => {
            if (client.element) {
                client.element.style.display = 'block';
            }
        });

        // إظهار/إخفاء حالة فارغة
        if (emptyState) {
            emptyState.style.display = clients.length === 0 ? 'block' : 'none';
        }

        // تحديث معلومات التصفح
        updatePagination(clients.length);
    }

    // تحديث التصفح
    function updatePagination(totalItems) {
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo) {
            const start = (currentPage - 1) * itemsPerPage + 1;
            const end = Math.min(currentPage * itemsPerPage, totalItems);
            paginationInfo.textContent = `عرض ${start}-${end} من ${totalItems} عميل`;
        }
    }

    // وظائف العملاء
    window.viewClient = function(clientId) {
        showLoadingAnimation();

        // محاكاة تحميل البيانات
        setTimeout(() => {
            // يمكن إضافة نافذة منبثقة لعرض تفاصيل العميل
            window.location.href = `/clients/${clientId}`;
        }, 500);
    };

    window.editClient = function(clientId) {
        showLoadingAnimation();

        setTimeout(() => {
            // التوجه لصفحة التعديل
            window.location.href = `/clients/${clientId}/edit`;
        }, 500);
    };

    window.deleteClient = function(clientId) {
        const client = clientsData.find(c => c.id == clientId);
        if (client) {
            showDeleteModal(clientId, client.name);
        }
    };

    // إظهار نافذة حذف العميل
    function showDeleteModal(clientId, clientName) {
        if (deleteModal) {
            // تحديث محتوى النافذة
            const modalBody = deleteModal.querySelector('.modal-body p');
            if (modalBody) {
                modalBody.textContent = `هل أنت متأكد من حذف العميل "${clientName}"؟`;
            }

            // إظهار النافذة
            deleteModal.style.display = 'flex';
            deleteModal.classList.add('show');

            // إضافة مستمع للتأكيد
            const confirmBtn = deleteModal.querySelector('.btn-danger');
            if (confirmBtn) {
                confirmBtn.onclick = () => confirmDelete(clientId);
            }
        }
    }

    // تأكيد الحذف
    function confirmDelete(clientId) {
        showLoadingAnimation();

        // إرسال طلب الحذف باستخدام Laravel Form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/clients/${clientId}`;
        form.style.display = 'none';

        // إضافة CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // إضافة method DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    };

    // إغلاق النافذة المنبثقة
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    };

    // إعداد النوافذ المنبثقة
    function initModals() {
        // إغلاق النوافذ عند النقر خارجها
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target.id);
            }
        });

        // إغلاق النوافذ بالضغط على Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    closeModal(openModal.id);
                }
            }
        });
    }

    // تهيئة أزرار الأكشن
    function initActionButtons() {
        // زر إضافة عميل جديد
        if (addClientBtn) {
            addClientBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showLoadingAnimation();

                setTimeout(() => {
                    window.location.href = '/clients/create';
                }, 500);
            });
        }

        // زر التصدير
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                showLoadingAnimation();

                // محاكاة تصدير البيانات
                setTimeout(() => {
                    showSuccess('تم تصدير البيانات بنجاح');
                    hideLoadingAnimation();
                }, 1500);
            });
        }

        // زر الاستيراد
        if (importBtn) {
            importBtn.addEventListener('click', function() {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = '.csv,.xlsx,.xls';
                fileInput.onchange = function(e) {
                    if (e.target.files.length > 0) {
                        showLoadingAnimation();

                        setTimeout(() => {
                            showSuccess('تم استيراد البيانات بنجاح');
                            hideLoadingAnimation();
                        }, 2000);
                    }
                };
                fileInput.click();
            });
        }
    }

    // تهيئة التصفح
    function initPagination() {
        const paginationBtns = document.querySelectorAll('.page-btn');
        paginationBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('prev') || this.classList.contains('next')) {
                    return;
                }

                // تحديث الصفحة النشطة
                paginationBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                currentPage = parseInt(this.textContent);
                updateView();
            });
        });
    }

    // إظهار رسالة النجاح
    function showSuccess(message) {
        if (successMessage && successAlert) {
            successMessage.textContent = message;
            successAlert.style.display = 'block';
            successAlert.classList.add('show');

            // إخفاء الرسالة بعد 5 ثواني
            setTimeout(() => {
                successAlert.classList.remove('show');
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 300);
            }, 5000);
        }
    }

    // إظهار انيميشن التحميل
    function showLoadingAnimation() {
        // إضافة overlay للتحميل
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;

        const spinner = document.createElement('div');
        spinner.style.cssText = `
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        `;

        // إضافة CSS للانيميشن
        const clientsListStyle = document.createElement('style');
        clientsListStyle.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;

        document.head.appendChild(clientsListStyle);
        loadingOverlay.appendChild(spinner);
        document.body.appendChild(loadingOverlay);
    }

    // إخفاء انيميشن التحميل
    function hideLoadingAnimation() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    // إضافة تأثير النقر
    function addClickEffect(element) {
        element.style.transform = 'scale(0.98)';
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 150);
    }

    // تهيئة تأثيرات Hover
    function initHoverEffects() {
        const clientCards = document.querySelectorAll('.client-card');
        clientCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        });
    }

    // تهيئة تأثيرات الكيبورد
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl+F للبحث
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Ctrl+N لإضافة عميل جديد
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                if (addClientBtn) {
                    addClientBtn.click();
                }
            }
        });
    }

    // تهيئة الاستجابة للهاتف المحمول
    function initMobileResponsive() {
        // تحديث العرض عند تغيير حجم الشاشة
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                currentView = 'list';
                updateView();
            } else {
                currentView = 'grid';
                updateView();
            }
        });
    }

    // تهيئة تأثيرات الانيميشن
    function initAnimations() {
        // إضافة تأثير الظهور التدريجي
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        const clientCards = document.querySelectorAll('.client-card');
        clientCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            observer.observe(card);
        });
    }

    // تهيئة جميع الوظائف
    function initializeAll() {
        loadClientsFromLaravel();
        initSearch();
        initFilters();
        initViewToggle();
        initActionButtons();
        initPagination();
        initModals();
        initHoverEffects();
        initKeyboardShortcuts();
        initMobileResponsive();
        initAnimations();

        // إخفاء انيميشن التحميل الأولي
        hideLoadingAnimation();

        console.log('تم تهيئة جميع وظائف صفحة العملاء بنجاح');
    }

    // بدء التهيئة
    initializeAll();

    // تصدير الوظائف للاستخدام العام
    window.clientsManager = {
        refresh: loadClientsFromLaravel,
        search: performSearch,
        showSuccess: showSuccess,
        showLoading: showLoadingAnimation,
        hideLoading: hideLoadingAnimation
    };
});

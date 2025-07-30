document.addEventListener('DOMContentLoaded', function() {
    console.log('Cash Box JavaScript loaded successfully!');

    // المتغيرات العامة
    let currentPage = 1;
    let currentFilters = {};
    let isLoading = false;

    // تهيئة الصفحة
    function initializePage() {
        console.log('Initializing cash box page...');
        setupEventListeners();
        loadData();
        setupInitialBalance();
    }

    // إعداد المبلغ الأولي
    function setupInitialBalance() {
        const saveBtn = document.getElementById('saveInitialBalance');
        const input = document.getElementById('initialBalanceInput');

        if (saveBtn && input) {
            saveBtn.addEventListener('click', function() {
                const newBalance = parseFloat(input.value) || 0;
                saveInitialBalance(newBalance);
            });
        }
    }

    // حفظ المبلغ الأولي
    function saveInitialBalance(balance) {
        console.log('Saving initial balance:', balance);

        fetch(window.cashBoxInitialBalanceUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ initial_balance: balance })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('تم حفظ المبلغ الأولي بنجاح');
                document.getElementById('initialBalance').textContent = balance.toLocaleString('ar-SA', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                loadData(); // إعادة تحميل البيانات لتحديث الرصيد
            } else {
                showError(data.message || 'حدث خطأ أثناء حفظ المبلغ الأولي');
            }
        })
        .catch(error => {
            console.error('Error saving initial balance:', error);
            showError('حدث خطأ في الاتصال');
        });
    }

    // إعداد مستمعي الأحداث
    function setupEventListeners() {
        // نموذج الفلترة
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                collectFilters();
                loadData();
            });
        }

        // مستمع تغيير نوع الفلتر
        const typeSelect = document.getElementById('typeFilter');
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                const statusSelect = document.getElementById('statusFilter');

                if (selectedType === 'مصروف') {
                    // إذا تم اختيار "مصروف"، اجعل الحالة "مقبوضة" فقط
                    if (statusSelect) {
                        statusSelect.value = 'مقبوضة';
                        statusSelect.disabled = true;
                    }
                } else {
                    // إذا تم اختيار نوع آخر، أعد تفعيل اختيار الحالة
                    if (statusSelect) {
                        statusSelect.disabled = false;
                    }
                }
            });
        }

        // زر إعادة تعيين الفلاتر
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                resetFilters();
                currentPage = 1;
                loadData();
            });
        }

        // زر تبديل الفلاتر
        const toggleBtn = document.getElementById('toggleFilters');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const filterContent = document.getElementById('filterContent');
                if (filterContent) {
                    filterContent.style.display = filterContent.style.display === 'none' ? 'block' : 'none';
                }
            });
        }

        // زر تصدير البيانات
        const exportBtn = document.getElementById('exportData');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                exportData();
            });
        }
    }

    // جمع الفلاتر
    function collectFilters() {
        const form = document.getElementById('filterForm');
        if (!form) return {};

        const formData = new FormData(form);
        const type = formData.get('type') || '';

        currentFilters = {
            search: formData.get('search') || '',
            type: type,
            status: formData.get('status') || '',
            date_from: formData.get('date_from') || '',
            date_to: formData.get('date_to') || '',
            min_amount: formData.get('min_amount') || '',
            max_amount: formData.get('max_amount') || '',
            page: currentPage,
            per_page: 10
        };

        // إذا كان النوع "مصروف"، تأكد من أن الحالة "مقبوضة" فقط
        if (type === 'مصروف') {
            currentFilters.status = 'مقبوضة';
            // تحديث قيمة الحالة في النموذج
            const statusSelect = form.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.value = 'مقبوضة';
            }
        }

        console.log('Collected filters:', currentFilters);
    }

    // إعادة تعيين الفلاتر
    function resetFilters() {
        const form = document.getElementById('filterForm');
        if (form) {
            form.reset();

            // إعادة تفعيل جميع الحقول
            const statusSelect = document.getElementById('statusFilter');
            if (statusSelect) {
                statusSelect.disabled = false;
            }
        }
        currentFilters = {
            page: 1,
            per_page: 10
        };
    }

    // تحميل البيانات
    function loadData() {
        if (isLoading) return;

        isLoading = true;
        showLoading();

        console.log('Loading data with filters:', currentFilters);
        console.log('User ID from session:', window.currentUserId);

        // إضافة user_id للفلاتر
        const requestData = {
            ...currentFilters,
            user_id: window.currentUserId || null
        };

        fetch(window.cashBoxDataUrl + '?' + new URLSearchParams(requestData), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            credentials: 'include'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            hideLoading();
            updateTable(data.transactions || []);
            updateStats(data);
            updatePagination(data.pagination);

            if (data.message) {
                showInfo(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading data:', error);
            hideLoading();
            showError('حدث خطأ أثناء تحميل البيانات');

            // عرض رسالة في الجدول
            const tbody = document.getElementById('transactionsTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-red-500">
                            حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.
                        </td>
                    </tr>
                `;
            }
        })
        .finally(() => {
            isLoading = false;
        });
    }

    // تحديث الجدول
    function updateTable(transactions) {
        const tbody = document.getElementById('transactionsTableBody');
        if (!tbody) return;

        if (transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">
                        لا توجد بيانات لعرضها. يرجى إضافة فواتير أو مصاريف أولاً.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = transactions.map(transaction => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="text-center">${transaction.id}</td>
                <td>${transaction.name}</td>
                <td>
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getTypeBadgeClass(transaction.type)}">
                        ${transaction.type}
                    </span>
                </td>
                <td class="text-left font-medium">${formatAmount(transaction.amount)}</td>
                <td class="text-left">${transaction.debit || '-'}</td>
                <td class="text-left">${transaction.credit || '-'}</td>
                <td>
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getStatusBadgeClass(transaction.status)}">
                        ${transaction.status}
                    </span>
                </td>
                <td class="text-center">${formatDate(transaction.date)}</td>
                <td class="text-center">
                    ${transaction.source_type === 'invoice' ? `
                        <button class="btn btn-sm btn-info export-invoice-btn"
                                data-transaction-id="${transaction.id}"
                                data-source-id="${transaction.source_id}"
                                data-source-type="${transaction.source_type}"
                                title="تصدير الفاتورة">
                            <i class="fas fa-download"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `).join('');

        // إضافة مستمعي الأحداث لأزرار التصدير
        setupExportButtons();
    }

    // تحديث الإحصائيات
    function updateStats(data) {
        // تحديث الإحصائيات في البطاقات
        const totalRevenueEl = document.getElementById('totalRevenue');
        const totalExpensesEl = document.getElementById('totalExpenses');
        const balanceEl = document.getElementById('balance');

        if (totalRevenueEl) {
            totalRevenueEl.textContent = formatAmount(data.totalRevenue || 0);
        }
        if (totalExpensesEl) {
            totalExpensesEl.textContent = formatAmount(data.totalExpenses || 0);
        }
        if (balanceEl) {
            balanceEl.textContent = formatAmount(data.balance || 0);
        }
    }

    // تحديث الترقيم
    function updatePagination(pagination) {
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        if (paginationInfo && pagination) {
            const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
            const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
            paginationInfo.textContent = `عرض ${start}-${end} من ${pagination.total} نتيجة`;
        }

        if (paginationControls && pagination) {
            paginationControls.innerHTML = generatePaginationHTML(pagination);
        }
    }

    // إنشاء HTML للترقيم
    function generatePaginationHTML(pagination) {
        const { current_page, last_page, total } = pagination;

        if (last_page <= 1) return '';

        let html = '<div class="flex items-center space-x-2 space-x-reverse">';

        // زر السابق
        if (current_page > 1) {
            html += `
                <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        onclick="goToPage(${current_page - 1})">
                    السابق
                </button>
            `;
        }

        // أرقام الصفحات
        const startPage = Math.max(1, current_page - 2);
        const endPage = Math.min(last_page, current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            if (i === current_page) {
                html += `
                    <button class="px-3 py-1 text-sm bg-blue-500 text-white rounded">
                        ${i}
                    </button>
                `;
            } else {
                html += `
                    <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                            onclick="goToPage(${i})">
                        ${i}
                    </button>
                `;
            }
        }

        // زر التالي
        if (current_page < last_page) {
            html += `
                <button class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        onclick="goToPage(${current_page + 1})">
                    التالي
                </button>
            `;
        }

        html += '</div>';
        return html;
    }

    // الانتقال لصفحة معينة
    window.goToPage = function(page) {
        currentPage = page;
        currentFilters.page = page;
        loadData();
    };

    // تصدير البيانات
        function setupExportButtons() {
        // إضافة مستمعي الأحداث لأزرار تصدير الفواتير
        document.querySelectorAll('.export-invoice-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const sourceId = this.getAttribute('data-source-id');
                const sourceType = this.getAttribute('data-source-type');

                if (sourceType === 'invoice') {
                    exportInvoice(sourceId);
                }
            });
        });
    }

    function exportInvoice(invoiceId) {
        // التحقق من وجود مكتبة XLSX
        if (typeof XLSX === 'undefined') {
            showNotification('جاري تحميل مكتبة Excel...', 'info');

            // تحميل مكتبة XLSX
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
            script.onload = () => {
                exportInvoiceToExcel(invoiceId);
            };
            script.onerror = () => {
                showNotification('فشل في تحميل مكتبة Excel', 'error');
            };
            document.head.appendChild(script);
        } else {
            exportInvoiceToExcel(invoiceId);
        }
    }

    function exportInvoiceToExcel(invoiceId) {
        // جلب بيانات الفاتورة
        fetch(`/invoices/${invoiceId}/data`)
            .then(response => response.json())
            .then(data => {
                try {
                    // تجهيز بيانات الفاتورة للتصدير
                    const exportData = data.items.map(item => ({
                        'اسم المنتج': item.product_name,
                        'الكمية': item.quantity,
                        'السعر': item.price,
                        'الإجمالي': item.quantity * item.price
                    }));

                    // إضافة إجمالي الفاتورة
                    const totalAmount = data.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                    exportData.push({
                        'اسم المنتج': 'الإجمالي',
                        'الكمية': '',
                        'السعر': '',
                        'الإجمالي': totalAmount
                    });

                    // إنشاء ورقة عمل
                    const ws = XLSX.utils.json_to_sheet(exportData);

                    // تنسيق الأعمدة
                    const colWidths = [
                        { wch: 30 }, // اسم المنتج
                        { wch: 10 }, // الكمية
                        { wch: 15 }, // السعر
                        { wch: 15 }  // الإجمالي
                    ];
                    ws['!cols'] = colWidths;

                    // تنسيق الإجمالي
                    const lastRow = exportData.length;
                    const totalCell = XLSX.utils.encode_cell({ r: lastRow - 1, c: 3 }); // عمود الإجمالي
                    if (!ws[totalCell]) {
                        ws[totalCell] = { v: totalAmount, t: 'n' };
                    }
                    ws[totalCell].s = { font: { bold: true }, fill: { fgColor: { rgb: "FFFF00" } } };

                    // إنشاء مصنف
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'تفاصيل الفاتورة');

                    // إضافة معلومات الفاتورة
                    const info = [
                        ['تفاصيل الفاتورة'],
                        ['رقم الفاتورة:', data.invoice_number],
                        ['العميل:', data.client_name],
                        ['التاريخ:', formatDateForExcel(data.date)],
                        ['النوع:', data.type],
                        ['الحالة:', data.status],
                        ['إجمالي الفاتورة:', totalAmount],
                        ['عدد المنتجات:', data.items.length],
                        [''],
                        ['تم إنشاء هذا التقرير بواسطة نظام إدارة الفواتير']
                    ];

                    const infoWs = XLSX.utils.aoa_to_sheet(info);
                    XLSX.utils.book_append_sheet(wb, infoWs, 'معلومات الفاتورة');

                    // تصدير الملف
                    const fileName = `invoice-${data.invoice_number}-${new Date().toISOString().split('T')[0]}.xlsx`;
                    XLSX.writeFile(wb, fileName);

                    showNotification('تم تصدير الفاتورة إلى Excel بنجاح', 'success');
                } catch (error) {
                    console.error('خطأ في تصدير الفاتورة:', error);
                    showNotification('حدث خطأ أثناء تصدير الفاتورة', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ في جلب بيانات الفاتورة:', error);
                showNotification('حدث خطأ في جلب بيانات الفاتورة', 'error');
            });
    }

    function exportData() {
        // التحقق من وجود مكتبة XLSX
        if (typeof XLSX === 'undefined') {
            showNotification('جاري تحميل مكتبة Excel...', 'info');

            // تحميل مكتبة XLSX
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
            script.onload = () => {
                exportToExcel();
            };
            script.onerror = () => {
                showNotification('فشل في تحميل مكتبة Excel', 'error');
            };
            document.head.appendChild(script);
        } else {
            exportToExcel();
        }
    }

    function exportToExcel() {
        // جلب البيانات الحالية للتصدير
        const exportUrl = window.cashBoxDataUrl + '?' + new URLSearchParams({
            ...currentFilters,
            export: 'true',
            user_id: window.currentUserId || null
        });

        fetch(exportUrl)
            .then(response => response.json())
            .then(data => {
                try {
                    // تجهيز البيانات للتصدير
                    const exportData = data.transactions.map(transaction => ({
                        'التاريخ': formatDateForExcel(transaction.date),
                        'النوع': transaction.type,
                        'الوصف': transaction.name,
                        'المبلغ': transaction.amount,
                        'المبلغ بعد الحسم': transaction.amount_after_discount || transaction.amount,
                        'الحالة': transaction.status,
                        'مدين': transaction.debit || '',
                        'دائن': transaction.credit || '',
                        'مقبوضة': transaction.is_received ? 'نعم' : 'لا'
                    }));

                    // إضافة إجمالي في نهاية الجدول
                    const totalAmount = data.transactions.reduce((sum, transaction) => sum + transaction.amount, 0);
                    const totalAfterDiscount = data.transactions.reduce((sum, transaction) => sum + (transaction.amount_after_discount || transaction.amount), 0);

                    exportData.push({
                        'التاريخ': 'الإجمالي',
                        'النوع': '',
                        'الوصف': '',
                        'المبلغ': totalAmount,
                        'المبلغ بعد الحسم': totalAfterDiscount,
                        'الحالة': '',
                        'مدين': '',
                        'دائن': '',
                        'مقبوضة': ''
                    });

                    // إنشاء ورقة عمل
                    const ws = XLSX.utils.json_to_sheet(exportData);

                    // تنسيق الأعمدة
                    const colWidths = [
                        { wch: 15 }, // التاريخ
                        { wch: 12 }, // النوع
                        { wch: 40 }, // الوصف
                        { wch: 15 }, // المبلغ
                        { wch: 15 }, // المبلغ بعد الحسم
                        { wch: 12 }, // الحالة
                        { wch: 15 }, // مدين
                        { wch: 15 }, // دائن
                        { wch: 10 }  // مقبوضة
                    ];
                    ws['!cols'] = colWidths;

                    // تنسيق الإجمالي
                    const lastRow = exportData.length;
                    const totalRow = lastRow;
                    const totalCell = XLSX.utils.encode_cell({ r: totalRow - 1, c: 3 }); // عمود المبلغ
                    const totalAfterDiscountCell = XLSX.utils.encode_cell({ r: totalRow - 1, c: 4 }); // عمود المبلغ بعد الحسم

                    if (!ws[totalCell]) {
                        ws[totalCell] = { v: totalAmount, t: 'n' };
                    }
                    if (!ws[totalAfterDiscountCell]) {
                        ws[totalAfterDiscountCell] = { v: totalAfterDiscount, t: 'n' };
                    }

                    ws[totalCell].s = { font: { bold: true }, fill: { fgColor: { rgb: "FFFF00" } } };
                    ws[totalAfterDiscountCell].s = { font: { bold: true }, fill: { fgColor: { rgb: "FFFF00" } } };

                    // إنشاء مصنف
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'حركات الصندوق');

                    // إضافة معلومات إضافية
                    const info = [
                        ['تقرير حركات الصندوق'],
                        ['تاريخ التصدير:', new Date().toLocaleDateString('ar-SA')],
                        ['إجمالي المبالغ:', totalAmount],
                        ['إجمالي المبالغ بعد الحسم:', totalAfterDiscount],
                        ['عدد الحركات:', data.transactions.length],
                        ['الرصيد الأولي:', data.initial_balance || 0],
                        ['الرصيد الحالي:', data.current_balance || 0],
                        [''],
                        ['تم إنشاء هذا التقرير بواسطة نظام إدارة الفواتير']
                    ];

                    const infoWs = XLSX.utils.aoa_to_sheet(info);
                    XLSX.utils.book_append_sheet(wb, infoWs, 'معلومات التقرير');

                    // تصدير الملف
                    const fileName = `cash-box-${new Date().toISOString().split('T')[0]}.xlsx`;
                    XLSX.writeFile(wb, fileName);

                    showNotification('تم تصدير البيانات إلى Excel بنجاح', 'success');
                } catch (error) {
                    console.error('خطأ في تصدير البيانات:', error);
                    showNotification('حدث خطأ أثناء تصدير البيانات', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ في جلب البيانات:', error);
                showNotification('حدث خطأ في جلب البيانات', 'error');
            });
    }

    function formatDateForExcel(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    // دوال مساعدة
    function formatAmount(amount) {
        return parseFloat(amount || 0).toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA');
    }

    function getTypeBadgeClass(type) {
        const classes = {
            'بيع': 'bg-green-100 text-green-800',
            'شراء': 'bg-blue-100 text-blue-800',
            'مردودات بيع': 'bg-red-100 text-red-800',
            'مردودات شراء': 'bg-yellow-100 text-yellow-800',
            'مصروف': 'bg-purple-100 text-purple-800'
        };
        return classes[type] || 'bg-gray-100 text-gray-800';
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'مقبوضة': 'bg-green-100 text-green-800',
            'غير مقبوضة': 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    function showLoading() {
        const loadingMessage = document.getElementById('loadingMessage');
        const loadingOverlay = document.getElementById('loadingOverlay');

        if (loadingMessage) loadingMessage.style.display = 'block';
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        const loadingMessage = document.getElementById('loadingMessage');
        const loadingOverlay = document.getElementById('loadingOverlay');

        if (loadingMessage) loadingMessage.style.display = 'none';
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }

    function showSuccess(message) {
        showNotification(message, 'success');
    }

    function showError(message) {
        showNotification(message, 'error');
    }

    function showInfo(message) {
        showNotification(message, 'info');
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // إضافة CSS للرسائل
    const cashBoxStyle = document.createElement('style');
    cashBoxStyle.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(cashBoxStyle);

    // بدء التطبيق
    initializePage();
});

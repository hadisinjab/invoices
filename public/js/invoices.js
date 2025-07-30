/**
 * نظام إدارة الفواتير - الواجهة الأمامية
 * Invoice Management System - Frontend
 */

// حذف بيانات sampleInvoices والمتغيرات المرتبطة بها
// إضافة متغير لتخزين الفواتير القادمة من السيرفر
let serverInvoices = [];

// متغيرات النظام
let currentPage = 1;
let itemsPerPage = 10;
let filteredInvoices = [...serverInvoices];
let selectedInvoices = [];
let currentSortField = 'date';
let currentSortDirection = 'desc';
let currentAction = null;
let currentInvoiceId = null;

// تحديث دالة InvoiceManager
class InvoiceManager {
    constructor() {
        this.invoices = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.fetchInvoices(); // جلب الفواتير من السيرفر عند التحميل
        this.updateStats();
        this.showLoadingBar();
    }

    setupEventListeners() {
        // البحث والفلترة
        document.getElementById('searchInput')?.addEventListener('input', this.handleSearch.bind(this));
        document.getElementById('typeFilter')?.addEventListener('change', this.handleFilter.bind(this));
        document.getElementById('statusFilter')?.addEventListener('change', this.handleFilter.bind(this));
        document.getElementById('clientFilter')?.addEventListener('change', this.handleFilter.bind(this));
        document.getElementById('dateFromFilter')?.addEventListener('change', this.handleFilter.bind(this));
        document.getElementById('dateToFilter')?.addEventListener('change', this.handleFilter.bind(this));

        // أزرار الإجراءات
        document.getElementById('addInvoiceBtn')?.addEventListener('click', this.handleAddInvoice.bind(this));
        document.getElementById('resetFiltersBtn')?.addEventListener('click', this.resetFilters.bind(this));
        document.getElementById('exportBtn')?.addEventListener('click', this.exportData.bind(this));
        document.getElementById('printBtn')?.addEventListener('click', this.printTable.bind(this));

        // تحديد الكل
        document.getElementById('selectAll')?.addEventListener('change', this.handleSelectAll.bind(this));

        // النوافذ المنبثقة
        document.getElementById('closeModal')?.addEventListener('click', this.closeModal.bind(this));
        document.getElementById('cancelBtn')?.addEventListener('click', this.closeModal.bind(this));
        document.getElementById('confirmBtn')?.addEventListener('click', this.confirmAction.bind(this));

        // إغلاق النافذة المنبثقة عند النقر خارجها
        document.getElementById('confirmModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'confirmModal') {
                this.closeModal();
            }
        });

        // التنقل بين الصفحات
        this.setupPagination();
    }

    showLoadingBar() {
        const loadingBar = document.getElementById('loadingBar');
        if (loadingBar) {
            loadingBar.classList.add('active');
            setTimeout(() => {
                loadingBar.classList.remove('active');
            }, 1000);
        }
    }

    fetchInvoices() {
        const params = this.getFilterParams();
        this.showLoadingBar();

        // إضافة timestamp لمنع الكاش
        const url = '/invoice/public/dashboard/invoices/api?' + new URLSearchParams(params).toString() + '&_t=' + Date.now();

        console.log('Fetching invoices from:', url);

        axios.get(url)
            .then(response => {
                console.log('Invoices response:', response.data);
                this.invoices = response.data.invoices || [];
                filteredInvoices = [...this.invoices];
                this.sortInvoices();
                currentPage = 1;
                this.loadInvoices();
                this.updateStats();
            })
            .catch(error => {
                console.error('Error fetching invoices:', error);
                console.error('Error response:', error.response);
                console.error('Error config:', error.config);

                this.invoices = [];
                filteredInvoices = [];
                this.loadInvoices();
                this.updateStats();

                if (error.response && error.response.status === 404) {
                    this.showNotification('المسار غير موجود. تأكد من تسجيل الدخول.', 'error');
                    console.log('404 Error - URL not found');
                } else if (error.response && error.response.status === 401) {
                    this.showNotification('يجب تسجيل الدخول أولاً.', 'error');
                    setTimeout(() => {
                        window.location.href = '/invoice/public/login';
                    }, 2000);
                } else {
                    this.showNotification('حدث خطأ أثناء جلب الفواتير: ' + (error.message || 'خطأ غير معروف'), 'error');
                }
            });
    }

    getFilterParams() {
        // جمع باراميترات الفلترة من عناصر الصفحة
        return {
            search: document.getElementById('searchInput')?.value || '',
            type: document.getElementById('typeFilter')?.value || '',
            status: document.getElementById('statusFilter')?.value || '',
            client_id: document.getElementById('clientFilter')?.value || '',
            date: document.getElementById('dateFromFilter')?.value || '', // يمكن تطويرها لدعم dateTo لاحقًا
        };
    }

    handleSearch(e) {
        this.fetchInvoices();
    }

    handleFilter() {
        this.fetchInvoices();
    }

    sortInvoices() {
        filteredInvoices.sort((a, b) => {
            let aValue = a[currentSortField];
            let bValue = b[currentSortField];

            if (currentSortField === 'date') {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } else if (currentSortField === 'amount') {
                aValue = parseFloat(aValue);
                bValue = parseFloat(bValue);
            }

            if (currentSortDirection === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });
    }

    loadInvoices() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentInvoices = filteredInvoices.slice(startIndex, endIndex);

        this.renderInvoices(currentInvoices);
        this.updatePagination();
        this.updateResultsCount();
    }

    renderInvoices(invoices) {
        const tbody = document.querySelector('#invoicesTable tbody');
        if (!tbody) return;

        if (invoices.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-state">
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <h3>لا توجد فواتير</h3>
                            <p>لم يتم العثور على فواتير تطابق معايير البحث المحددة</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = invoices.map(invoice => `
            <tr>
                <td class="checkbox-container">
                    <input type="checkbox" value="${invoice.id}" onchange="invoiceManager.handleInvoiceSelect(this)">
                </td>
                <td><strong>${invoice.number}</strong></td>
                <td>${invoice.client}</td>
                <td>${this.formatDate(invoice.date)}</td>
                <td><span class="type-badge type-${this.getTypeClass(invoice.type)}">${invoice.type}</span></td>
                <td><strong>${this.formatCurrency(invoice.amount)}</strong></td>
                <td><span class="status-badge status-${this.getStatusClass(invoice.status)}">${invoice.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" onclick="invoiceManager.viewInvoice(${invoice.id})" title="عرض">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" onclick="invoiceManager.editInvoice(${invoice.id})" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn print" onclick="invoiceManager.printInvoice(${invoice.id})" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="action-btn download" onclick="invoiceManager.downloadInvoice(${invoice.id})" title="تحميل">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="action-btn delete" onclick="invoiceManager.deleteInvoice(${invoice.id})" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    getTypeClass(type) {
        const typeClasses = {
            'بيع': 'sale',
            'شراء': 'purchase',
            'مردودات بيع': 'return-sale',
            'مردودات شراء': 'return-purchase'
        };
        return typeClasses[type] || '';
    }

    getStatusClass(status) {
        const statusClasses = {
            'مقبوضة': 'paid',
            'غير مقبوضة': 'unpaid',
            'متأخرة': 'overdue',
            'مسودة': 'draft'
        };
        return statusClasses[status] || '';
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('ar-EG', {
            style: 'currency',
            currency: 'EGP'
        }).format(amount);
    }

    updateStats() {
        const totalInvoices = this.invoices.length;
        const paidInvoices = this.invoices.filter(inv => inv.status === 'مقبوضة').length;
        const unpaidInvoices = this.invoices.filter(inv => inv.status === 'غير مقبوضة').length;
        const totalAmount = this.invoices.reduce((sum, inv) => sum + inv.amount, 0);

        // تحديث البطاقات الإحصائية
        document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = totalInvoices.toLocaleString();
        document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = paidInvoices.toLocaleString();
        document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = unpaidInvoices.toLocaleString();
        document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = this.formatCurrency(totalAmount);
    }

    updatePagination() {
        const totalPages = Math.ceil(filteredInvoices.length / itemsPerPage);
        const pagination = document.querySelector('.pagination');
        if (!pagination) return;

        let paginationHTML = '';

        // زر السابق
        paginationHTML += `
            <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="invoiceManager.goToPage(${currentPage - 1})">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        // أرقام الصفحات
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            paginationHTML += `<button class="page-btn" onclick="invoiceManager.goToPage(1)">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<span class="page-dots">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="invoiceManager.goToPage(${i})">
                    ${i}
                </button>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<span class="page-dots">...</span>`;
            }
            paginationHTML += `<button class="page-btn" onclick="invoiceManager.goToPage(${totalPages})">${totalPages}</button>`;
        }

        // زر التالي
        paginationHTML += `
            <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="invoiceManager.goToPage(${currentPage + 1})">
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        pagination.innerHTML = paginationHTML;
    }

    updateResultsCount() {
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, filteredInvoices.length);
        const total = filteredInvoices.length;

        document.querySelector('.results-count').textContent = `عرض ${start}-${end} من ${total}`;
        document.querySelector('.pagination-info span').textContent = `عرض ${start}-${end} من ${total} فاتورة`;
    }

    goToPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredInvoices.length / itemsPerPage)) {
            currentPage = page;
            this.loadInvoices();
        }
    }

    setupPagination() {
        // إعداد التنقل بين الصفحات
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' && currentPage < Math.ceil(filteredInvoices.length / itemsPerPage)) {
                this.goToPage(currentPage + 1);
            } else if (e.key === 'ArrowRight' && currentPage > 1) {
                this.goToPage(currentPage - 1);
            }
        });
    }

    handleInvoiceSelect(checkbox) {
        const invoiceId = parseInt(checkbox.value);
        if (checkbox.checked) {
            selectedInvoices.push(invoiceId);
        } else {
            selectedInvoices = selectedInvoices.filter(id => id !== invoiceId);
        }
        this.updateSelectAll();
        this.updateBulkActions();
    }

    handleSelectAll(e) {
        const checkboxes = document.querySelectorAll('#invoicesTable tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
            this.handleInvoiceSelect(checkbox);
        });
    }

    updateSelectAll() {
        const allCheckboxes = document.querySelectorAll('#invoicesTable tbody input[type="checkbox"]');
        const checkedCheckboxes = document.querySelectorAll('#invoicesTable tbody input[type="checkbox"]:checked');
        const selectAllCheckbox = document.getElementById('selectAll');

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }
    }

    updateBulkActions() {
        const bulkActions = document.querySelector('.bulk-actions');
        if (bulkActions) {
            if (selectedInvoices.length > 0) {
                bulkActions.classList.add('active');
            } else {
                bulkActions.classList.remove('active');
            }
        }
    }

    resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('clientFilter').value = '';
        document.getElementById('dateFromFilter').value = '';
        this.fetchInvoices();
        this.showNotification('تم إعادة تعيين المرشحات', 'success');
    }

    handleAddInvoice() {
        // في Laravel، يمكن توجيه المستخدم لصفحة إنشاء فاتورة جديدة
        window.location.href = '/invoices/create';
    }

    viewInvoice(id) {
        // توجيه لصفحة عرض الفاتورة
        window.location.href = `/invoices/${id}`;
    }

    editInvoice(id) {
        // توجيه لصفحة تعديل الفاتورة
        window.location.href = `/invoices/${id}/edit`;
    }

    printInvoice(id) {
        // طباعة الفاتورة
        window.open(`/invoices/${id}/print`, '_blank');
    }

    downloadInvoice(id) {
        // تحميل الفاتورة كـ PDF
        this.showLoadingBar();
        window.location.href = `/invoices/${id}/download`;
    }

    // تم تعطيل جميع دوال الحذف الديناميكي حتى لا يتعارض مع كود Blade
    // deleteInvoice(id) {}
    // performDelete(id) {}

    showModal(message) {
        const modal = document.getElementById('confirmModal');
        const modalBody = modal.querySelector('.modal-body p');
        modalBody.textContent = message;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        const modal = document.getElementById('confirmModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        currentAction = null;
        currentInvoiceId = null;
    }

    confirmAction() {
        if (currentAction === 'delete' && currentInvoiceId) {
            this.performDelete(currentInvoiceId);
        }
        this.closeModal();
    }

    // تم تعطيل جميع دوال الحذف الديناميكي حتى لا يتعارض مع كود Blade
    // performDelete(id) {}

    exportData() {
        // التحقق من وجود فاتورة محددة
        const selectedInvoice = document.querySelector('input[type="checkbox"][value]:checked');
        if (!selectedInvoice) {
            this.showNotification('يرجى اختيار فاتورة واحدة للتصدير', 'error');
            return;
        }

        const invoiceId = selectedInvoice.value;

        // التحقق من وجود مكتبة XLSX
        if (typeof XLSX === 'undefined') {
            this.showNotification('جاري تحميل مكتبة Excel...', 'info');

            // تحميل مكتبة XLSX
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
            script.onload = () => {
                this.exportInvoiceToExcel(invoiceId);
            };
            script.onerror = () => {
                this.showNotification('فشل في تحميل مكتبة Excel', 'error');
            };
            document.head.appendChild(script);
        } else {
            this.exportInvoiceToExcel(invoiceId);
        }
    }

    exportInvoiceToExcel(invoiceId) {
        // جلب بيانات الفاتورة المحددة
        fetch(`/invoices/${invoiceId}/data`)
            .then(response => response.json())
            .then(data => {
                try {
                    // تجهيز بيانات الفاتورة للتصدير
                    const exportData = data.items.map(item => ({
                        'اسم المنتج': item.product_name,
                        'الكمية': item.quantity,
                        'السعر': item.price,
                        'الإجمالي': item.total
                    }));

                    // إضافة إجمالي الفاتورة
                    const totalAmount = data.items.reduce((sum, item) => sum + item.total, 0);
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
                        ['التاريخ:', this.formatDateForExcel(data.date)],
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

                    this.showNotification('تم تصدير الفاتورة إلى Excel بنجاح', 'success');
                } catch (error) {
                    console.error('خطأ في تصدير الفاتورة:', error);
                    this.showNotification('حدث خطأ أثناء تصدير الفاتورة', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ في جلب بيانات الفاتورة:', error);
                this.showNotification('حدث خطأ في جلب بيانات الفاتورة', 'error');
            });
    }

    formatDateForExcel(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    printTable() {
        // طباعة الجدول
        const printWindow = window.open('', '_blank');
        const tableContent = document.querySelector('.table-container').innerHTML;

        printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl">
            <head>
                <title>طباعة قائمة الفواتير</title>
                <style>
                    body { font-family: Arial, sans-serif; direction: rtl; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
                    th { background-color: #f2f2f2; }
                    .action-buttons { display: none; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <h1>قائمة الفواتير</h1>
                <p>تاريخ الطباعة: ${new Date().toLocaleDateString('ar-EG')}</p>
                ${tableContent}
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.print();

        this.showNotification('تم إعداد الطباعة', 'success');
    }

    showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        const notificationContent = notification.querySelector('.notification-content');

        // تحديث الرسالة والنوع
        notificationContent.querySelector('span').textContent = message;
        notification.className = `notification ${type}`;

        // تحديث الأيقونة
        const icon = notificationContent.querySelector('i');
        const iconClasses = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        icon.className = iconClasses[type] || iconClasses.success;

        // إظهار الإشعار
        notification.classList.add('show');

        // إخفاء الإشعار بعد 3 ثوانٍ
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
}

// إنشاء مثيل من مدير الفواتير
let invoiceManager;

// تشغيل التطبيق عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    invoiceManager = new InvoiceManager();

    // إضافة أي إعدادات إضافية خاصة بـ Laravel
    setupLaravelIntegration();
});

// إعداد التكامل مع Laravel
function setupLaravelIntegration() {
    // إعداد CSRF Token لجميع طلبات AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        // إعداد axios أو fetch للاستخدام مع CSRF
        window.axios = window.axios || {};
        if (window.axios.defaults) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        }
    }

    // إعداد التعامل مع الجلسات والرسائل
    setupSessionMessages();

    // إعداد التحقق من الصحة
    setupValidation();
}

// إعداد رسائل الجلسة
function setupSessionMessages() {
    // عرض رسائل النجاح أو الخطأ من Laravel
    const successMessage = document.querySelector('.alert-success')?.textContent;
    const errorMessage = document.querySelector('.alert-error')?.textContent;

    if (successMessage) {
        setTimeout(() => {
            invoiceManager.showNotification(successMessage, 'success');
        }, 500);
    }

    if (errorMessage) {
        setTimeout(() => {
            invoiceManager.showNotification(errorMessage, 'error');
        }, 500);
    }
}

// إعداد التحقق من الصحة
function setupValidation() {
    // إعداد التحقق من صحة النماذج
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!validateForm(form)) {
                e.preventDefault();
                invoiceManager.showNotification('يرجى تصحيح الأخطاء في النموذج', 'error');
            }
        });
    });
}

// دالة التحقق من صحة النموذج
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// دوال مساعدة للتصدير
window.invoiceManager = null;

// تصدير الدوال للاستخدام العام
window.InvoiceManager = InvoiceManager;

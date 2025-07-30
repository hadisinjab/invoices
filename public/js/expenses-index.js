// متغيرات عامة
let expenses = [];
let filteredExpenses = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentSort = { field: '', direction: '' };
let currentView = 'table';
let deleteItemId = null;
let lastQueryParams = {};
let pagination = { current_page: 1, last_page: 1, per_page: 10, total: 0 };
let statistics = { totalExpenses: 0, todayExpenses: 0, weekExpenses: 0, expensesCount: 0 };

// تهيئة الصفحة
document.addEventListener('DOMContentLoaded', function() {
    fetchExpenses();
    setupEventListeners();
    updateView();
    // تأثير الظهور التدريجي
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
    });
});

// جلب المصاريف من API
function fetchExpenses(params = {}) {
    // حفظ آخر باراميترات
    lastQueryParams = { ...params };
    // إعداد باراميترات الاستعلام
    const query = new URLSearchParams({
        page: params.page || currentPage,
        per_page: itemsPerPage,
        search: params.search || '',
        date: params.date || '',
        amount: params.amount || '',
        sort: params.sort || currentSort.field || '',
        dir: params.dir || currentSort.direction || '',
    });
    fetch('/invoice/public/api/expenses?' + query.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        expenses = data.expenses || [];
        filteredExpenses = expenses;
        pagination = data.pagination;
        statistics = data.statistics;
        currentPage = pagination.current_page;
        updateStatistics();
        renderExpenses();
        updatePagination();
    })
    .catch(err => {
        console.error('Error fetching expenses:', err);
        console.error('Error response:', err.response);

        if (err.response && err.response.status === 404) {
            showNotification('المسار غير موجود. تأكد من تسجيل الدخول.', 'error');
        } else if (err.response && err.response.status === 401) {
            showNotification('يجب تسجيل الدخول أولاً.', 'error');
            setTimeout(() => {
                window.location.href = '/invoice/public/login';
            }, 2000);
        } else {
            showNotification('فشل في جلب البيانات من السيرفر: ' + (err.message || 'خطأ غير معروف'), 'error');
        }
    });
}

// تهيئة المصاريف من السيرفر
function initializeExpenses() {
    // محاولة جلب البيانات من السيرفر أولاً
    fetchExpenses({ page: 1 });

    // إذا فشل الاتصال بالسيرفر، استخدم البيانات المحلية
    const storedExpenses = localStorage.getItem('expenses');
    if (storedExpenses) {
        expenses = JSON.parse(storedExpenses);
        filteredExpenses = [...expenses];
        updateStatistics();
        renderExpenses();
    }
}

// إعداد مستمعي الأحداث
function setupEventListeners() {
    // البحث
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearch, 300));
    }

    // التصفية
    const dateFilter = document.getElementById('dateFilter');
    const amountFilter = document.getElementById('amountFilter');

    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }

    if (amountFilter) {
        amountFilter.addEventListener('change', applyFilters);
    }

    // الترتيب
    const sortableHeaders = document.querySelectorAll('.sortable');
    if (sortableHeaders && sortableHeaders.length > 0) {
        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => handleSort(header.dataset.sort));
        });
    }
}

// حفظ المصاريف في localStorage
function saveExpenses() {
    localStorage.setItem('expenses', JSON.stringify(expenses));
}

// تحديث الإحصائيات
function updateStatistics() {
    const today = new Date().toISOString().split('T')[0];
    const oneWeekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    const totalExpenses = expenses.reduce((sum, expense) => sum + expense.amount, 0);
    const todayExpenses = expenses
        .filter(expense => expense.date === today)
        .reduce((sum, expense) => sum + expense.amount, 0);
    const weekExpenses = expenses
        .filter(expense => expense.date >= oneWeekAgo)
        .reduce((sum, expense) => sum + expense.amount, 0);
    const expensesCount = expenses.length;

    // تحديث العناصر مع تأثير العد
    animateNumber('totalExpenses', totalExpenses);
    animateNumber('todayExpenses', todayExpenses);
    animateNumber('weekExpenses', weekExpenses);
    animateNumber('expensesCount', expensesCount);
}

// تأثير العد المتحرك للأرقام
function animateNumber(elementId, targetValue) {
    const element = document.getElementById(elementId);
    const startValue = parseInt(element.textContent) || 0;
    const duration = 1000;
    const startTime = Date.now();

    function updateNumber() {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // تأثير easing
        const easedProgress = 1 - Math.pow(1 - progress, 3);
        const currentValue = Math.floor(startValue + (targetValue - startValue) * easedProgress);

        if (elementId === 'expensesCount') {
            element.textContent = currentValue.toLocaleString('ar-SA');
        } else {
            element.textContent = currentValue.toLocaleString('ar-SA', {
                style: 'currency',
                currency: 'SAR',
                minimumFractionDigits: 0
            });
        }

        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }

    updateNumber();
}

// البحث
function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    fetchExpenses({ ...lastQueryParams, search: searchTerm, page: 1 });
}

// تطبيق التصفية
function applyFilters() {
    const dateFilter = document.getElementById('dateFilter').value;
    const amountFilter = document.getElementById('amountFilter').value;
    fetchExpenses({ ...lastQueryParams, date: dateFilter, amount: amountFilter, page: 1 });
}

// إعادة تعيين التصفية
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('amountFilter').value = '';
    currentSort = { field: '', direction: '' };
    fetchExpenses({ page: 1 });
    // إعادة تعيين أيقونات الترتيب
    document.querySelectorAll('.sort-icon').forEach(icon => {
        icon.parentElement.classList.remove('sort-asc', 'sort-desc');
    });
}

// الترتيب
function handleSort(field) {
    if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
    }
    fetchExpenses({ ...lastQueryParams, sort: currentSort.field, dir: currentSort.direction, page: 1 });
    // تحديث أيقونات الترتيب
    document.querySelectorAll('.sortable').forEach(header => {
        header.classList.remove('sort-asc', 'sort-desc');
        const icon = header.querySelector('.sort-icon');
        if (icon) {
            icon.className = 'fas fa-sort sort-icon';
        }
    });
    const currentHeader = document.querySelector(`[data-sort="${field}"]`);
    if (currentHeader) {
        currentHeader.classList.add(`sort-${currentSort.direction}`);
        const icon = currentHeader.querySelector('.sort-icon');
        if (icon) {
            if (currentSort.direction === 'asc') {
                icon.className = 'fas fa-sort-up sort-icon';
            } else {
                icon.className = 'fas fa-sort-down sort-icon';
            }
        }
    }
}

// تغيير العرض بين الجدول والبطاقات
function toggleView() {
    currentView = currentView === 'table' ? 'cards' : 'table';
    updateView();
}

// تحديث العرض
function updateView() {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const viewIcon = document.getElementById('viewIcon');
    const viewText = document.getElementById('viewText');

    if (currentView === 'table') {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        viewIcon.className = 'fas fa-th-large ml-1';
        viewText.textContent = 'عرض البطاقات';
    } else {
        tableView.classList.add('hidden');
        cardView.classList.remove('hidden');
        viewIcon.className = 'fas fa-table ml-1';
        viewText.textContent = 'عرض الجدول';
    }

    renderExpenses();
    updatePagination();
}

// عرض المصاريف (بدون تقطيع محلي)
function renderExpenses() {
    if (!expenses.length) {
        showEmptyState();
        return;
    }
    hideEmptyState();
    if (currentView === 'table') {
        renderTable(expenses);
    } else {
        renderCards(expenses);
    }
}

// عرض الجدول
function renderTable(expensesToRender) {
    const tableBody = document.getElementById('expensesTableBody');
    tableBody.innerHTML = '';

    expensesToRender.forEach((expense, index) => {
        const row = document.createElement('tr');
        row.style.animationDelay = `${index * 0.05}s`;
        row.className = 'fade-in';

        row.innerHTML = `
            <td class="font-semibold text-gray-800">${escapeHtml(expense.name)}</td>
            <td class="font-bold text-blue-600">${formatCurrency(expense.amount)}</td>
            <td class="text-gray-600">${formatDate(expense.date)}</td>
            <td class="text-gray-600 max-w-xs truncate" title="${escapeHtml(expense.comment || '')}">${escapeHtml(expense.comment || 'لا يوجد تعليق')}</td>
            <td>
                <div class="flex gap-2">
                    <button class="action-btn view" onclick="viewExpense(${expense.id})" title="عرض التفاصيل">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit" onclick="editExpense(${expense.id})" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="showDeleteModal(${expense.id})" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

// عرض البطاقات
function renderCards(expensesToRender) {
    const cardsContainer = document.getElementById('expensesCardsContainer');
    cardsContainer.innerHTML = '';

    expensesToRender.forEach((expense, index) => {
        const card = document.createElement('div');
        card.className = 'expense-card fade-in';
        card.style.animationDelay = `${index * 0.1}s`;

        card.innerHTML = `
            <div class="expense-card-header">
                <h3 class="expense-card-title">${escapeHtml(expense.name)}</h3>
                <span class="expense-card-amount">${formatCurrency(expense.amount)}</span>
            </div>
            <div class="expense-card-date">${formatDate(expense.date)}</div>
            <div class="expense-card-comment">${escapeHtml(expense.comment || 'لا يوجد تعليق')}</div>
            <div class="expense-card-actions">
                <button class="action-btn view" onclick="viewExpense(${expense.id})" title="عرض التفاصيل">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit" onclick="editExpense(${expense.id})" title="تعديل">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete" onclick="showDeleteModal(${expense.id})" title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        cardsContainer.appendChild(card);
    });
}

// عرض الحالة الفارغة
function showEmptyState() {
    document.getElementById('emptyState').classList.remove('hidden');
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('cardView').classList.add('hidden');
    document.getElementById('paginationContainer').innerHTML = '';
}

// إخفاء الحالة الفارغة
function hideEmptyState() {
    document.getElementById('emptyState').classList.add('hidden');
}

// تحديث التنقل بين الصفحات
function updatePagination() {
    const totalPages = pagination.last_page;
    const paginationContainer = document.getElementById('paginationContainer');
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    let paginationHTML = '';
    // زر الصفحة السابقة
    paginationHTML += `
        <button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    // أرقام الصفحات
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    if (startPage > 1) {
        paginationHTML += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span class="px-2">...</span>`;
        }
    }
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">
                ${i}
            </button>
        `;
    }
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<span class="px-2">...</span>`;
        }
        paginationHTML += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
    }
    // زر الصفحة التالية
    paginationHTML += `
        <button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})">
            <i class="fas fa-chevron-left"></i>
        </button>
    `;
    paginationContainer.innerHTML = paginationHTML;
}

// الانتقال لصفحة معينة
function goToPage(page) {
    fetchExpenses({ ...lastQueryParams, page });
}

// عرض تفاصيل المصروف
function viewExpense(id) {
    const expense = expenses.find(exp => exp.id === id);
    if (!expense) return;

    const detailsContent = `
        <div class="expense-detail-item">
            <span class="expense-detail-label">اسم المستلزم:</span>
            <span class="expense-detail-value">${escapeHtml(expense.name)}</span>
        </div>
        <div class="expense-detail-item">
            <span class="expense-detail-label">المبلغ:</span>
            <span class="expense-detail-value font-bold text-blue-600">${formatCurrency(expense.amount)}</span>
        </div>
        <div class="expense-detail-item">
            <span class="expense-detail-label">التاريخ:</span>
            <span class="expense-detail-value">${formatDate(expense.date)}</span>
        </div>
        <div class="expense-detail-item">
            <span class="expense-detail-label">التعليق:</span>
            <span class="expense-detail-value">${escapeHtml(expense.comment || 'لا يوجد تعليق')}</span>
        </div>
        <div class="expense-detail-item">
            <span class="expense-detail-label">رقم المعرف:</span>
            <span class="expense-detail-value">#${expense.id}</span>
        </div>
    `;

    document.getElementById('expenseDetails').innerHTML = detailsContent;
    document.getElementById('detailsModal').classList.remove('hidden');
}

// تعديل المصروف
function editExpense(id) {
    // البحث عن المصروف في المصفوفة
    const expense = expenses.find(e => e.id === id);
    if (!expense) {
        showNotification('لم يتم العثور على المصروف', 'error');
        return;
    }

    // ملء النموذج بالبيانات الحالية
    const form = document.getElementById('editExpenseForm');
    form.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="block mb-1 font-semibold text-gray-700">اسم المستلزم <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editName" class="form-input w-full" value="${escapeHtml(expense.name)}" required>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="editAmount" class="form-input w-full" value="${expense.amount}" min="0" step="0.01" required>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                <input type="date" name="date" id="editDate" class="form-input w-full" value="${expense.expense_date}" required>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">التعليق</label>
                <textarea name="comment" id="editComment" class="form-input w-full" rows="2">${escapeHtml(expense.comment || '')}</textarea>
            </div>
        </div>
    `;

    // إضافة معرف المصروف للنموذج
    form.dataset.expenseId = id;

    // فتح النافذة المنبثقة
    document.getElementById('editModal').classList.remove('hidden');
}

// إظهار نافذة تأكيد الحذف
function showDeleteModal(id) {
    deleteItemId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// إغلاق نافذة الحذف
function closeDeleteModal() {
    deleteItemId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

// إغلاق نافذة التعديل
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editExpenseForm').innerHTML = '';
}

// حفظ تعديل المصروف
function saveExpenseEdit() {
    const form = document.getElementById('editExpenseForm');
    const expenseId = form.dataset.expenseId;

    if (!expenseId) {
        showNotification('خطأ في معرف المصروف', 'error');
        return;
    }

    const nameInput = document.getElementById('editName');
    const amountInput = document.getElementById('editAmount');
    const dateInput = document.getElementById('editDate');
    const commentInput = document.getElementById('editComment');

    // التحقق من صحة البيانات
    if (!nameInput.value.trim()) {
        showNotification('يرجى إدخال اسم المستلزم', 'error');
        return;
    }

    if (!amountInput.value || isNaN(amountInput.value) || Number(amountInput.value) <= 0) {
        showNotification('يرجى إدخال مبلغ صحيح أكبر من صفر', 'error');
        return;
    }

    if (!dateInput.value) {
        showNotification('يرجى اختيار التاريخ', 'error');
        return;
    }

    // تجهيز البيانات للتحديث
    const updatedExpense = {
        name: nameInput.value.trim(),
        amount: parseFloat(amountInput.value),
        expense_date: dateInput.value,
        comment: commentInput.value.trim()
    };

    // إرسال طلب التحديث
    fetch(`/invoice/public/expenses/${expenseId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(updatedExpense)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // تحديث البيانات المحلية
            const expenseIndex = expenses.findIndex(e => e.id === parseInt(expenseId));
            if (expenseIndex !== -1) {
                expenses[expenseIndex] = { ...expenses[expenseIndex], ...updatedExpense };
            }

            const filteredIndex = filteredExpenses.findIndex(e => e.id === parseInt(expenseId));
            if (filteredIndex !== -1) {
                filteredExpenses[filteredIndex] = { ...filteredExpenses[filteredIndex], ...updatedExpense };
            }

            // إعادة عرض البيانات
            renderExpenses();
            updateStatistics();

            // إغلاق النافذة المنبثقة
            closeEditModal();

            showNotification('تم تحديث المصروف بنجاح', 'success');
        } else {
            showNotification(data.message || 'حدث خطأ أثناء تحديث المصروف', 'error');
        }
    })
    .catch(() => {
        showNotification('حدث خطأ في الاتصال', 'error');
    });
}

// تأكيد الحذف
function confirmDelete() {
    if (deleteItemId) {
        expenses = expenses.filter(expense => expense.id !== deleteItemId);
        filteredExpenses = filteredExpenses.filter(expense => expense.id !== deleteItemId);

        saveExpenses();
        updateStatistics();

        // إذا كانت الصفحة الحالية فارغة، انتقل للصفحة السابقة
        const totalPages = Math.ceil(filteredExpenses.length / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = totalPages;
        }

        renderExpenses();
        updatePagination();
        closeDeleteModal();

        // إظهار رسالة نجاح
        showNotification('تم حذف المصروف بنجاح', 'success');
    }
}

// إغلاق نافذة التفاصيل
function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// تصدير البيانات إلى Excel
function exportExpenses() {
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
    try {
        // تجهيز البيانات للتصدير
        const exportData = expenses.map(expense => ({
            'اسم المستلزم': expense.name,
            'المبلغ': expense.amount,
            'التاريخ': formatDateForExcel(expense.expense_date),
            'التعليق': expense.comment || '',
            'تاريخ الإنشاء': formatDateForExcel(expense.created_at)
        }));

        // إضافة إجمالي في نهاية الجدول
        const totalAmount = expenses.reduce((sum, expense) => sum + expense.amount, 0);
        exportData.push({
            'اسم المستلزم': 'الإجمالي',
            'المبلغ': totalAmount,
            'التاريخ': '',
            'التعليق': '',
            'تاريخ الإنشاء': ''
        });

        // إنشاء ورقة عمل
        const ws = XLSX.utils.json_to_sheet(exportData);

        // تنسيق الأعمدة
        const colWidths = [
            { wch: 25 }, // اسم المستلزم
            { wch: 15 }, // المبلغ
            { wch: 15 }, // التاريخ
            { wch: 30 }, // التعليق
            { wch: 20 }  // تاريخ الإنشاء
        ];
        ws['!cols'] = colWidths;

        // تنسيق الإجمالي
        const lastRow = exportData.length;
        const totalRow = lastRow;
        const totalCell = XLSX.utils.encode_cell({ r: totalRow - 1, c: 1 }); // عمود المبلغ
        if (!ws[totalCell]) {
            ws[totalCell] = { v: totalAmount, t: 'n' };
        }
        ws[totalCell].s = { font: { bold: true }, fill: { fgColor: { rgb: "FFFF00" } } };

        // إنشاء مصنف
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'المصاريف');

        // إضافة معلومات إضافية
        const info = [
            ['تقرير المصاريف'],
            ['تاريخ التصدير:', new Date().toLocaleDateString('ar-SA')],
            ['إجمالي المصاريف:', totalAmount],
            ['عدد المصاريف:', expenses.length],
            [''],
            ['تم إنشاء هذا التقرير بواسطة نظام إدارة الفواتير']
        ];

        const infoWs = XLSX.utils.aoa_to_sheet(info);
        XLSX.utils.book_append_sheet(wb, infoWs, 'معلومات التقرير');

        // تصدير الملف
        const fileName = `expenses-${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, fileName);

        showNotification('تم تصدير البيانات إلى Excel بنجاح', 'success');
    } catch (error) {
        console.error('خطأ في تصدير البيانات:', error);
        showNotification('حدث خطأ أثناء تصدير البيانات', 'error');
    }
}

// تنسيق التاريخ للتصدير
function formatDateForExcel(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

// إظهار الإشعارات
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-semibold transform translate-x-full transition-all duration-300`;

    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500');
            break;
        case 'error':
            notification.classList.add('bg-red-500');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500');
            break;
        default:
            notification.classList.add('bg-blue-500');
    }

    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation' : 'info'}-circle"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(notification);

    // إظهار الإشعار
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // إخفاء الإشعار
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// تنسيق العملة
function formatCurrency(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 2
    }).format(amount);
}

// تنسيق التاريخ
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// تأمين النص من XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Debounce function لتحسين الأداء
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// إغلاق النوافذ المنبثقة عند الضغط على ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
        closeDetailsModal();
    }
});

// إعادة تحميل البيانات عند العودة للصفحة
window.addEventListener('focus', function() {
    // التحقق من وجود تحديثات في البيانات
    const storedExpenses = localStorage.getItem('expenses');
    if (storedExpenses) {
        const newExpenses = JSON.parse(storedExpenses);
        if (JSON.stringify(newExpenses) !== JSON.stringify(expenses)) {
            expenses = newExpenses;
            filteredExpenses = [...expenses];
            updateStatistics();
            renderExpenses();
            updatePagination();
        }
    }
});

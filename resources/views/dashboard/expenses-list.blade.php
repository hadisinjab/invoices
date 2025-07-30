@extends('layouts.app')

@section('content')
<div class="min-h-screen p-6">
    <!-- رأس الصفحة -->
    <div class="glass-card mb-8 fade-in">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="icon-wrapper">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">إدارة المصاريف</h1>
                    <p class="text-gray-600 mt-1">تتبع وإدارة جميع مصاريفك بسهولة</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('expenses.create') }}" class="btn-primary">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مصروف جديد
                </a>
                <button class="btn-secondary" onclick="exportExpenses()">
                    <i class="fas fa-download ml-2"></i>
                    تصدير البيانات
                </button>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card fade-in" style="animation-delay: 0.1s">
            <div class="stats-icon bg-blue-500">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800" id="totalExpenses">0</h3>
                <p class="text-gray-600">إجمالي المصاريف</p>
            </div>
        </div>
        <div class="stats-card fade-in" style="animation-delay: 0.2s">
            <div class="stats-icon bg-green-500">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800" id="todayExpenses">0</h3>
                <p class="text-gray-600">مصاريف اليوم</p>
            </div>
        </div>
        <div class="stats-card fade-in" style="animation-delay: 0.3s">
            <div class="stats-icon bg-yellow-500">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800" id="weekExpenses">0</h3>
                <p class="text-gray-600">مصاريف الأسبوع</p>
            </div>
        </div>
        <div class="stats-card fade-in" style="animation-delay: 0.4s">
            <div class="stats-icon bg-purple-500">
                <i class="fas fa-list"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800" id="expensesCount">0</h3>
                <p class="text-gray-600">عدد المصاريف</p>
            </div>
        </div>
    </div>

    <!-- أدوات البحث والتصفية -->
    <div class="glass-card mb-6 fade-in" style="animation-delay: 0.5s">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="البحث في المصاريف..." class="search-input">
            </div>
            <div>
                <select id="dateFilter" class="filter-select">
                    <option value="">جميع التواريخ</option>
                    <option value="today">اليوم</option>
                    <option value="yesterday">أمس</option>
                    <option value="week">هذا الأسبوع</option>
                    <option value="month">هذا الشهر</option>
                    <option value="year">هذا العام</option>
                </select>
            </div>
            <!-- تم حذف فلترة المبلغ هنا -->
            <div class="flex gap-2">
                <button class="btn-outline" onclick="resetFilters()">
                    <i class="fas fa-undo ml-1"></i>
                    إعادة تعيين
                </button>
                <button class="btn-outline" onclick="toggleView()">
                    <i class="fas fa-th-large ml-1" id="viewIcon"></i>
                    <span id="viewText">عرض الجدول</span>
                </button>
            </div>
        </div>
    </div>

    <!-- عرض البيانات -->
    <div class="glass-card fade-in" style="animation-delay: 0.6s">
        <!-- عرض الجدول -->
        <div id="tableView" class="table-container">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="name">
                            اسم المستلزم
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="amount">
                            المبلغ
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="date">
                            التاريخ
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>التعليق</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="expensesTableBody">
                    <!-- البيانات ستضاف هنا عبر JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- عرض البطاقات -->
        <div id="cardView" class="cards-container hidden">
            <div id="expensesCardsContainer">
                <!-- البطاقات ستضاف هنا عبر JavaScript -->
            </div>
        </div>

        <!-- رسالة فارغة -->
        <div id="emptyState" class="empty-state hidden">
            <div class="empty-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h3>لا توجد مصاريف حتى الآن</h3>
            <p>ابدأ بإضافة مصروفك الأول</p>
            <a href="{{ route('expenses.create') }}" class="btn-primary mt-4">
                <i class="fas fa-plus ml-2"></i>
                إضافة مصروف جديد
            </a>
        </div>
    </div>

    <!-- صفحات التنقل -->
    <div class="pagination-wrapper fade-in" style="animation-delay: 0.7s">
        <div class="pagination" id="paginationContainer">
            <!-- أزرار التنقل ستضاف هنا عبر JavaScript -->
        </div>
    </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div id="deleteModal" class="modal hidden">
    <div class="modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>تأكيد الحذف</h3>
            <button onclick="closeDeleteModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p>هل أنت متأكد من حذف هذا المصروف؟</p>
            <p class="text-sm text-gray-500">لا يمكن التراجع عن هذا الإجراء</p>
        </div>
        <div class="modal-footer">
            <button onclick="closeDeleteModal()" class="btn-outline">إلغاء</button>
            <button onclick="confirmDelete()" class="btn-danger">
                <i class="fas fa-trash ml-2"></i>
                حذف
            </button>
        </div>
    </div>
</div>

<!-- نافذة تفاصيل المصروف -->
<div id="detailsModal" class="modal hidden">
    <div class="modal-overlay" onclick="closeDetailsModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>تفاصيل المصروف</h3>
            <button onclick="closeDetailsModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="expenseDetails">
            <!-- التفاصيل ستضاف هنا عبر JavaScript -->
        </div>
    </div>
</div>

<!-- نافذة تعديل المصروف -->
<div id="editModal" class="modal hidden">
    <div class="modal-overlay" onclick="closeEditModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>تعديل المصروف</h3>
            <button onclick="closeEditModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editExpenseForm" onsubmit="event.preventDefault(); saveExpenseEdit();">
                <!-- النموذج سيضاف هنا عبر JavaScript -->
            </form>
        </div>
        <div class="modal-footer">
            <button onclick="closeEditModal()" class="btn-outline">إلغاء</button>
            <button onclick="saveExpenseEdit()" class="btn-primary">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/expenses-index.css') }}" rel="stylesheet">
<style>
    /* أنماط إضافية للنوافذ المنبثقة */
    .modal-content {
        max-width: 500px;
        width: 90%;
    }

    .modal-body form {
        padding: 1rem;
    }

    .modal-footer {
        padding: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('js/expenses-index.js') }}"></script>
@endpush

@endsection

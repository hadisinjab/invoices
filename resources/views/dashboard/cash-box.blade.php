@extends('layouts.app')

@section('title', 'الصندوق وجدول الحساب')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cash-box.css') }}">
    <style>
        /* تأكد من أن الجدول مرئي */
        .data-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .data-table th,
        .data-table td {
            padding: 12px 16px !important;
            border: 1px solid #e5e7eb !important;
            text-align: right !important;
        }
        .data-table th {
            background: #f9fafb !important;
            font-weight: 600 !important;
        }
        .table-container {
            overflow-x: auto !important;
            min-height: 200px !important;
        }
    </style>
@endpush

@section('content')
<div class="cash-box-container">
    <!-- العنوان الرئيسي -->
    <div class="page-header fade-in">
        <h1 class="page-title">
            <i class="fas fa-cash-register"></i>
            الصندوق وجدول الحساب
        </h1>
        <p class="page-subtitle">إدارة ومتابعة الحركات المالية والصندوق</p>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid fade-in">
        <div class="stat-card card-success">
            <div class="stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <div class="initial-balance-section">
                    <h3 class="stat-value" id="initialBalance">{{ number_format($cashBox->initial_balance ?? 0, 2) }}</h3>
                    <p class="stat-label">المبلغ الأولي للصندوق</p>
                    <div class="initial-balance-form">
                        <div class="input-group">
                            <input type="number" id="initialBalanceInput" placeholder="أدخل المبلغ الأولي" step="0.01" min="0" value="{{ $cashBox->initial_balance ?? 0 }}">
                            <button type="button" id="saveInitialBalance" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i>
                                حفظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card card-danger">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="totalRevenue">{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                <p class="stat-label">الإيرادات</p>
            </div>
        </div>

        <div class="stat-card card-warning">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="totalExpenses">{{ number_format($totalExpenses ?? 0, 2) }}</h3>
                <p class="stat-label">المصاريف</p>
            </div>
        </div>

        <div class="stat-card card-gradient">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-value" id="balance">{{ number_format(($cashBox->initial_balance ?? 0) + (($totalRevenue ?? 0) - ($totalExpenses ?? 0)), 2) }}</h3>
                <p class="stat-label">الرصيد الحالي</p>
            </div>
        </div>
    </div>

    <!-- رسالة حالة التحميل -->
    <div id="loadingMessage" class="text-center py-4 text-gray-500" style="display: none;">
        جاري تحميل البيانات...
    </div>

    <!-- بطاقة الفلترة والبحث -->
    <div class="filter-section fade-in">
        <div class="filter-card">
            <div class="filter-header">
                <h3 class="filter-title">
                    <i class="fas fa-filter"></i>
                    البحث والفلترة
                </h3>
                <button class="btn btn-secondary" id="toggleFilters">
                    <i class="fas fa-chevron-down"></i>
                    إظهار/إخفاء الفلاتر
                </button>
            </div>

            <div class="filter-content" id="filterContent">
                <form id="filterForm" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="search">البحث</label>
                            <div class="input-group">
                                <i class="fas fa-search"></i>
                                <input type="text" id="search" name="search" placeholder="البحث في الاسم أو الوصف...">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="type">النوع</label>
                            <select id="type" name="type">
                                <option value="">جميع الأنواع</option>
                                <option value="بيع">بيع</option>
                                <option value="شراء">شراء</option>
                                <option value="مردودات بيع">مردودات بيع</option>
                                <option value="مردودات شراء">مردودات شراء</option>
                                <option value="مصروف">مصروف</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="status">الحالة</label>
                            <select id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="مقبوضة">مقبوضة</option>
                                <option value="غير مقبوضة">غير مقبوضة</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="dateFrom">من تاريخ</label>
                            <input type="date" id="dateFrom" name="date_from">
                        </div>

                        <div class="filter-group">
                            <label for="dateTo">إلى تاريخ</label>
                            <input type="date" id="dateTo" name="date_to">
                        </div>

                        <div class="filter-group">
                            <label for="minAmount">الحد الأدنى للمبلغ</label>
                            <input type="number" id="minAmount" name="min_amount" placeholder="0.00" step="0.01">
                        </div>

                        <div class="filter-group">
                            <label for="maxAmount">الحد الأقصى للمبلغ</label>
                            <input type="number" id="maxAmount" name="max_amount" placeholder="0.00" step="0.01">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            البحث
                        </button>
                        <button type="button" class="btn btn-secondary" id="resetFilters">
                            <i class="fas fa-refresh"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول الحساب -->
    <div class="table-section fade-in">
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-table"></i>
                    جدول الحساب
                </h3>
                <div class="table-actions">
                    <button class="btn btn-info" id="exportData">
                        <i class="fas fa-download"></i>
                        تصدير البيانات
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="transactionsTable">
                    <thead>
                        <tr>
                            <th data-sort="id">#</th>
                            <th data-sort="name">الاسم</th>
                            <th data-sort="type">النوع</th>
                            <th data-sort="amount">المبلغ</th>
                            <th data-sort="debit">المدين</th>
                            <th data-sort="credit">الدائن</th>
                            <th data-sort="status">الحالة</th>
                            <th data-sort="date">التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">
                                جاري تحميل البيانات...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    <span id="paginationInfo">عرض 1-10 من 100 نتيجة</span>
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- أزرار التنقل ستُضاف هنا عبر JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>جاري التحميل...</p>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // تعريف المتغيرات العامة
        window.cashBoxDataUrl = '{{ route("cash-box.data") }}';
        window.cashBoxInitialBalanceUrl = '{{ route("cash-box.initial-balance") }}';
        window.csrfToken = '{{ csrf_token() }}';
        window.currentUserId = {{ Auth::id() ?? 'null' }};
    </script>
    <script src="{{ asset('js/cash-box.js') }}"></script>
@endpush

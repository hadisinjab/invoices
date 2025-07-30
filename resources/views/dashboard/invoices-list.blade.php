@extends('layouts.app')
@section('title', 'قائمة الفواتير - نظام الفواتير')
@section('content')
<!-- شريط التحميل -->
<div class="loading-bar" id="loadingBar"></div>
<!-- الحاوية الرئيسية -->
<div class="main-container">
    <!-- رأس الصفحة -->
    <header class="page-header">
        <div class="header-content">
            <div class="header-left">
                <div class="page-title">
                    <i class="fas fa-file-invoice-dollar icon"></i>
                    <h1>قائمة الفواتير</h1>
                </div>
                <div class="breadcrumb">
                    <a href="/invoice/public/dashboard">الرئيسية</a>
                    <i class="fas fa-chevron-left"></i>
                    <span>قائمة الفواتير</span>
                </div>
            </div>
            <div class="header-right">
                <a href="/invoice/public/dashboard/invoice/create" class="btn btn-primary" id="addInvoiceBtn">
                    <i class="fas fa-plus"></i>
                    إنشاء فاتورة جديدة
                </a>
            </div>
        </div>
    </header>
    <!-- إحصائيات سريعة -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-content">
                <h3>إجمالي الفواتير</h3>
                <p class="stat-number">{{ $invoiceCount }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>فواتير مقبوضة</h3>
                <p class="stat-number">{{ $invoicePaidCount }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>فواتير غير مقبوضة</h3>
                <p class="stat-number">{{ $invoiceUnpaidCount }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <h3>المبلغ الإجمالي</h3>
                <p class="stat-number">{{ number_format($totalSalesPaid, 2) }}</p>
            </div>
        </div>
    </div>
    <!-- قسم الفلترة والبحث -->
    <form method="GET" id="filterForm">
        <div class="filter-section">
            <div class="filter-card">
                <div class="filter-header">
                    <h3>
                        <i class="fas fa-filter"></i>
                        فلترة وبحث
                    </h3>
                    <button class="btn btn-secondary" type="button" onclick="window.location='{{ route('invoices.index') }}'">
                        <i class="fas fa-undo"></i>
                        إعادة تعيين
                    </button>
                </div>
                <div class="filter-content">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>البحث</label>
                            <div class="search-input">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" id="searchInput" placeholder="بحث برقم الفاتورة أو اسم العميل..." value="{{ request('search') }}" onkeydown="if(event.key==='Enter'){this.form.submit();}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label>نوع الفاتورة</label>
                            <select name="type" id="typeFilter" onchange="this.form.submit()">
                                <option value="">كل الأنواع</option>
                                <option value="شراء" {{ request('type')=='شراء'?'selected':'' }}>شراء</option>
                                <option value="بيع" {{ request('type')=='بيع'?'selected':'' }}>بيع</option>
                                <option value="مردودات شراء" {{ request('type')=='مردودات شراء'?'selected':'' }}>مردودات شراء</option>
                                <option value="مردودات بيع" {{ request('type')=='مردودات بيع'?'selected':'' }}>مردودات بيع</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>حالة الفاتورة</label>
                            <select name="status" id="statusFilter" onchange="this.form.submit()">
                                <option value="">كل الحالات</option>
                                <option value="مقبوضة" {{ request('status')=='مقبوضة'?'selected':'' }}>مقبوضة</option>
                                <option value="غير مقبوضة" {{ request('status')=='غير مقبوضة'?'selected':'' }}>غير مقبوضة</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>تاريخ من</label>
                            <input type="date" name="date" id="dateFromFilter" value="{{ request('date') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- جدول الفواتير -->
    <div class="table-section">
        <div class="table-header">
            <div class="table-title">
                <h3>
                    <i class="fas fa-table"></i>
                    الفواتير
                </h3>
                <span class="results-count">عرض 1-10 من 1,234</span>
            </div>
            <div class="table-actions">
                <button class="btn btn-outline" id="exportBtn">
                    <i class="fas fa-download"></i>
                    تصدير
                </button>
                <button class="btn btn-outline" id="printBtn">
                    <i class="fas fa-print"></i>
                    طباعة
                </button>
            </div>
        </div>
        <div class="table-container">
            <table class="invoices-table" id="invoicesTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr data-invoice-id="{{ $invoice->id }}">
                            <td class="checkbox-container">
                                <input type="checkbox" value="{{ $invoice->id }}">
                            </td>
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->client->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->date)->format('Y-m-d') }}</td>
                            <td><span class="type-badge type-{{ $invoice->type == 'بيع' ? 'sale' : ($invoice->type == 'شراء' ? 'purchase' : ($invoice->type == 'مردودات بيع' ? 'return-sale' : 'return-purchase')) }}">{{ $invoice->type }}</span></td>
                            <td><strong>{{ number_format($invoice->total, 2) }}</strong></td>
                            <td><span class="status-badge status-{{ $invoice->status == 'مقبوضة' ? 'paid' : ($invoice->status == 'غير مقبوضة' ? 'unpaid' : 'draft') }}">{{ $invoice->status }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="action-btn view" href="{{ route('invoices.show', $invoice->id) }}" title="عرض"><i class="fas fa-eye"></i></a>
                                    <a class="action-btn edit" href="{{ route('invoices.edit', $invoice->id) }}" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="action-btn delete" data-id="{{ $invoice->id }}" title="حذف"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div style="text-align: center; padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                                    <h3>لا توجد فواتير</h3>
                                    <p>لم يتم العثور على فواتير</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- التنقل بين الصفحات -->
        <div class="pagination-container">
            <div class="pagination-info">
                <span>
                    عرض {{ $invoices->firstItem() }}-{{ $invoices->lastItem() }} من {{ $invoices->total() }} فاتورة
                </span>
            </div>
            <div class="pagination">
                @if ($invoices->onFirstPage())
                    <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                @else
                    <a href="{{ $invoices->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                @endif
                @foreach ($invoices->getUrlRange(1, $invoices->lastPage()) as $page => $url)
                    @if ($page == $invoices->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @elseif ($page == 1 || $page == $invoices->lastPage() || abs($page - $invoices->currentPage()) <= 1)
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @elseif ($page == 2 && $invoices->currentPage() > 3)
                        <span class="page-dots">...</span>
                    @elseif ($page == $invoices->lastPage() - 1 && $invoices->currentPage() < $invoices->lastPage() - 2)
                        <span class="page-dots">...</span>
                    @endif
                @endforeach
                @if ($invoices->hasMorePages())
                    <a href="{{ $invoices->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                @else
                    <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- نافذة منبثقة للتأكيد -->
<div class="modal" id="confirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تأكيد الإجراء</h3>
            <button class="modal-close" id="closeModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>هل أنت متأكد من أنك تريد تنفيذ هذا الإجراء؟</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelBtn">إلغاء</button>
            <button class="btn btn-danger" id="confirmBtn">تأكيد</button>
        </div>
    </div>
</div>
<!-- نافذة منبثقة للإشعارات -->
<div class="notification" id="notification">
    <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span>تم تنفيذ الإجراء بنجاح</span>
    </div>
</div>
@endsection
@push('styles')
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/invoices.css') }}">
    <link href="{{ asset('css/invoices-list.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('js/axios.min.js') }}"></script>
<script src="{{ asset('js/invoices.js') }}"></script>
@endpush

@extends('layouts.app')

@section('content')
<div class="clients-page">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h2 class="page-title">
                    <i class="fas fa-users"></i>
                    قائمة العملاء
                </h2>
                <div class="breadcrumb">
                    <span><i class="fas fa-home"></i> الرئيسية</span>
                    <span><i class="fas fa-chevron-left"></i></span>
                    <span class="active">العملاء</span>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $clients->count() }}</div>
                    <div class="stat-label">إجمالي العملاء</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-section">
            <form method="GET" action="{{ route('clients.index') }}" class="search-form">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="البحث عن عميل بالاسم أو البريد الإلكتروني..."
                           class="search-input">
                    @if(request('search'))
                        <a href="{{ route('clients.index') }}" class="search-clear">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="action-buttons">
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                إضافة عميل جديد
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Content Area -->
    <div class="content-area">
        @if($clients->count() == 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>لا يوجد عملاء بعد</h3>
                <p>ابدأ بإضافة عميلك الأول لتتمكن من إنشاء الفواتير</p>
                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة عميل جديد
                </a>
            </div>
        @else
            <div class="clients-grid">
                @foreach($clients as $client)
                    <div class="client-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="client-header">
                            <div class="client-avatar">
                                <span>{{ mb_substr($client->name, 0, 1) }}</span>
                            </div>
                            <div class="client-info">
                                <h3 class="client-name">{{ $client->name }}</h3>
                                <div class="client-email">{{ $client->email ?? 'لا يوجد بريد إلكتروني' }}</div>
                            </div>
                        </div>

                        <div class="client-details">
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $client->email ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $client->phone ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $client->address ?? '-' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-globe"></i>
                                <span>{{ $client->country ?? '-' }} @if($client->city), {{ $client->city }}@endif</span>
                            </div>
                        </div>

                        <div class="client-actions">
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل
                            </a>
                            <button type="button" class="btn btn-cashbox" onclick="openCashBoxModal({{ $client->id }}, '{{ $client->name }}')">
                                <i class="fas fa-cash-register"></i>
                                الصندوق
                            </button>
                            <form action="{{ route('clients.destroy', $client->id) }}"
                                  method="POST"
                                  class="delete-form"
                                  onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا العميل؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($clients->hasPages())
                <div class="pagination-container">
                    {{ $clients->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Custom CSS -->
<style>
    /* Main Page Styles */
    .clients-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Header Styles */
    .page-header {
        margin-bottom: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .breadcrumb .active {
        font-weight: 600;
    }

    .header-stats {
        text-align: center;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .search-section {
        flex: 1;
        min-width: 300px;
    }

    .search-form {
        width: 100%;
    }

    .search-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        right: 15px;
        color: #666;
        z-index: 2;
    }

    .search-input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid #e1e5e9;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-clear {
        position: absolute;
        left: 15px;
        color: #999;
        text-decoration: none;
        padding: 5px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .search-clear:hover {
        background: #f0f0f0;
        color: #666;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: none;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-edit {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
    }

    .btn-delete {
        background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
    }

    /* Alert Styles */
    .alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 15px;
        position: relative;
        animation: slideInDown 0.5s ease-out;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-close {
        position: absolute;
        left: 15px;
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: background 0.3s ease;
    }

    .alert-close:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #666;
    }

    .empty-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .empty-state p {
        font-size: 1rem;
        margin-bottom: 30px;
        opacity: 0.7;
    }

    /* Clients Grid */
    .clients-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .client-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .client-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .client-header {
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #f0f0f0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .client-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .client-name {
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #333;
    }

    .client-email {
        color: #666;
        font-size: 0.9rem;
    }

    .client-details {
        padding: 25px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .detail-item i {
        width: 20px;
        color: #667eea;
        font-size: 0.9rem;
    }

    .detail-item span {
        color: #555;
        font-size: 0.95rem;
    }

    .client-actions {
        padding: 20px 25px;
        background: #f8f9fa;
        display: flex;
        gap: 10px;
        border-top: 1px solid #e9ecef;
    }

    .delete-form {
        display: inline-block;
    }

    /* Pagination */
    .pagination-container {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .clients-page {
            padding: 0 15px;
        }

        .page-header {
            padding: 20px;
        }

        .header-content {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-section {
            min-width: auto;
        }

        .clients-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .client-actions {
            flex-direction: column;
        }
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .client-card {
        animation: fadeInUp 0.6s ease-out;
    }

    /* أزرار الصندوق */
    .btn-cashbox {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cashbox:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    /* النافذة المنبثقة */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .modal.hidden {
        opacity: 0;
        pointer-events: none;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 90%;
        max-height: 90%;
        overflow: hidden;
        position: relative;
        z-index: 1;
        transform: scale(1);
        transition: transform 0.3s ease;
    }

    .modal.hidden .modal-content {
        transform: scale(0.9);
    }

    .cashbox-modal {
        width: 800px;
        max-height: 80vh;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-body {
        padding: 24px;
        max-height: 60vh;
        overflow-y: auto;
    }

    /* أزرار التبديل */
    .status-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        background: #f8f9fa;
        padding: 4px;
        border-radius: 12px;
    }

    .status-tab {
        flex: 1;
        background: none;
        border: none;
        padding: 12px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .status-tab.active {
        background: white;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .status-tab:hover:not(.active) {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    /* محتوى الحالة */
    .status-content {
        display: none;
    }

    .status-content.active {
        display: block;
    }

    /* جدول الفواتير */
    .invoices-table-container {
        margin-bottom: 24px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .invoices-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .invoices-table th {
        background: #f8f9fa;
        padding: 12px 16px;
        text-align: right;
        font-weight: 600;
        color: #495057;
        border-bottom: 1px solid #e9ecef;
    }

    .invoices-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f3f4;
        text-align: right;
    }

    .invoices-table tr:hover {
        background: #f8f9fa;
    }

    .no-data {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 24px;
    }

    /* أنواع الفواتير */
    .invoice-type {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .invoice-type.بيع {
        background: #d4edda;
        color: #155724;
    }

    .invoice-type.شراء {
        background: #f8d7da;
        color: #721c24;
    }

    .invoice-type.مردودات\ بيع {
        background: #fff3cd;
        color: #856404;
    }

    .invoice-type.مردودات\ شراء {
        background: #d1ecf1;
        color: #0c5460;
    }

    /* بطاقات الملخص */
    .summary-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .summary-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 1px solid #dee2e6;
    }

    .summary-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .summary-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #495057;
    }

    /* تحسينات للأزرار في بطاقة العميل */
    .client-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .client-actions .btn {
        flex: 1;
        min-width: 80px;
    }

    /* مؤشر التحميل */
    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        text-align: center;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 16px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-container p {
        color: #6c757d;
        font-size: 1rem;
        margin: 0;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .cashbox-modal {
            width: 95%;
            margin: 20px;
        }

        .modal-body {
            padding: 16px;
        }

        .status-tabs {
            flex-direction: column;
        }

        .summary-cards {
            grid-template-columns: 1fr;
        }

        .client-actions {
            flex-direction: column;
        }
    }
</style>

<!-- Custom JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit search form
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }

        // Enhanced delete confirmation
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const clientName = this.closest('.client-card').querySelector('.client-name').textContent;

                if (confirm(`هل أنت متأكد من حذف العميل "${clientName}"؟\nهذا الإجراء لا يمكن التراجع عنه.`)) {
                    this.submit();
                }
            });
        });

        // Card hover effects
        const clientCards = document.querySelectorAll('.client-card');
        clientCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Smooth transitions for buttons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Search input focus effects
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            searchInput.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        }
    });
</script>

<!-- نافذة الصندوق المنبثقة -->
<div id="cashBoxModal" class="modal hidden">
    <div class="modal-overlay" onclick="closeCashBoxModal()"></div>
    <div class="modal-content cashbox-modal">
        <div class="modal-header">
            <h3 id="cashBoxModalTitle">صندوق العميل</h3>
            <button onclick="closeCashBoxModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- أزرار التبديل -->
            <div class="status-tabs">
                <button class="status-tab active" onclick="switchStatus('received')" id="tab-received">
                    <i class="fas fa-check-circle"></i>
                    مقبوضة
                </button>
                <button class="status-tab" onclick="switchStatus('unreceived')" id="tab-unreceived">
                    <i class="fas fa-clock"></i>
                    غير مقبوضة
                </button>
            </div>

            <!-- محتوى الفواتير المقبوضة -->
            <div id="received-content" class="status-content active">
                <div class="invoices-table-container">
                    <table class="invoices-table" id="received-invoices-table">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="received-invoices-body">
                            <!-- سيتم ملؤها بالجافاسكريبت -->
                        </tbody>
                    </table>
                </div>
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-title">له</div>
                        <div class="summary-amount" id="received-credit">0.00</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-title">عليه</div>
                        <div class="summary-amount" id="received-debit">0.00</div>
                    </div>
                </div>
            </div>

            <!-- محتوى الفواتير غير المقبوضة -->
            <div id="unreceived-content" class="status-content">
                <div class="invoices-table-container">
                    <table class="invoices-table" id="unreceived-invoices-table">
                        <thead>
                            <tr>
                                <th>رقم الفاتورة</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody id="unreceived-invoices-body">
                            <!-- سيتم ملؤها بالجافاسكريبت -->
                        </tbody>
                    </table>
                </div>
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-title">له</div>
                        <div class="summary-amount" id="unreceived-credit">0.00</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-title">عليه</div>
                        <div class="summary-amount" id="unreceived-debit">0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript للصندوق -->
<script>
let currentClientId = null;
let currentClientName = null;

function openCashBoxModal(clientId, clientName) {
    currentClientId = clientId;
    currentClientName = clientName;
    document.getElementById('cashBoxModalTitle').textContent = `صندوق العميل: ${clientName}`;
    document.getElementById('cashBoxModal').classList.remove('hidden');
    loadClientInvoices(clientId);
}

function closeCashBoxModal() {
    document.getElementById('cashBoxModal').classList.add('hidden');
    currentClientId = null;
    currentClientName = null;
}

function switchStatus(status) {
    // إزالة الفئة النشطة من جميع الأزرار
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // إضافة الفئة النشطة للزر المحدد
    document.getElementById(`tab-${status}`).classList.add('active');

    // إخفاء جميع المحتويات
    document.querySelectorAll('.status-content').forEach(content => {
        content.classList.remove('active');
    });

    // إظهار المحتوى المحدد
    document.getElementById(`${status}-content`).classList.add('active');
}

function loadClientInvoices(clientId) {
    // إظهار حالة التحميل
    showLoading();

    fetch(`/invoice/public/api/clients/${clientId}/invoices`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoading();
            renderInvoices(data);
        })
        .catch(error => {
            hideLoading();
            console.error('Error loading invoices:', error);
            console.error('Error response:', error.response);

            if (error.response && error.response.status === 404) {
                showError('العميل غير موجود أو لا يمكن الوصول إليه');
            } else if (error.response && error.response.status === 401) {
                showError('يجب تسجيل الدخول أولاً');
                setTimeout(() => {
                    window.location.href = '/invoice/public/login';
                }, 2000);
            } else {
                showError('حدث خطأ أثناء تحميل البيانات: ' + (error.message || 'خطأ غير معروف'));
            }
        });
}

function renderInvoices(data) {
    // عرض الفواتير المقبوضة
    renderInvoicesTable('received', data.received || []);
    updateSummary('received', data.received || []);

    // عرض الفواتير غير المقبوضة
    renderInvoicesTable('unreceived', data.unreceived || []);
    updateSummary('unreceived', data.unreceived || []);
}

function renderInvoicesTable(status, invoices) {
    const tbody = document.getElementById(`${status}-invoices-body`);
    tbody.innerHTML = '';

    if (invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="no-data">لا توجد فواتير</td></tr>';
        return;
    }

    invoices.forEach(invoice => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${invoice.invoice_number}</td>
            <td><span class="invoice-type ${invoice.type}">${invoice.type}</span></td>
            <td>${formatCurrency(invoice.total)}</td>
            <td>${formatDate(invoice.date)}</td>
        `;
        tbody.appendChild(row);
    });
}

function updateSummary(status, invoices) {
    let credit = 0;
    let debit = 0;

    invoices.forEach(invoice => {
        if (invoice.type === 'شراء' || invoice.type === 'مردودات بيع') {
            credit += parseFloat(invoice.total);
        } else {
            debit += parseFloat(invoice.total);
        }
    });

    document.getElementById(`${status}-credit`).textContent = formatCurrency(credit);
    document.getElementById(`${status}-debit`).textContent = formatCurrency(debit);
}

function showLoading() {
    // إضافة مؤشر تحميل للنافذة المنبثقة
    const modalBody = document.querySelector('.modal-body');
    modalBody.innerHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p>جاري تحميل البيانات...</p>
        </div>
    `;
}

function hideLoading() {
    // إعادة تحميل المحتوى الأصلي
    const modalBody = document.querySelector('.modal-body');
    modalBody.innerHTML = `
        <!-- أزرار التبديل -->
        <div class="status-tabs">
            <button class="status-tab active" onclick="switchStatus('received')" id="tab-received">
                <i class="fas fa-check-circle"></i>
                مقبوضة
            </button>
            <button class="status-tab" onclick="switchStatus('unreceived')" id="tab-unreceived">
                <i class="fas fa-clock"></i>
                غير مقبوضة
            </button>
        </div>

        <!-- محتوى الفواتير المقبوضة -->
        <div id="received-content" class="status-content active">
            <div class="invoices-table-container">
                <table class="invoices-table" id="received-invoices-table">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody id="received-invoices-body">
                        <!-- سيتم ملؤها بالجافاسكريبت -->
                    </tbody>
                </table>
            </div>
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-title">له</div>
                    <div class="summary-amount" id="received-credit">0.00</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">عليه</div>
                    <div class="summary-amount" id="received-debit">0.00</div>
                </div>
            </div>
        </div>

        <!-- محتوى الفواتير غير المقبوضة -->
        <div id="unreceived-content" class="status-content">
            <div class="invoices-table-container">
                <table class="invoices-table" id="unreceived-invoices-table">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody id="unreceived-invoices-body">
                        <!-- سيتم ملؤها بالجافاسكريبت -->
                    </tbody>
                </table>
            </div>
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-title">له</div>
                    <div class="summary-amount" id="unreceived-credit">0.00</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">عليه</div>
                    <div class="summary-amount" id="unreceived-debit">0.00</div>
                </div>
            </div>
        </div>
    `;
}

function showError(message) {
    alert(message);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 2
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA');
}

// إغلاق النافذة عند الضغط على ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCashBoxModal();
    }
});
</script>
@endsection

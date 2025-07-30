@extends('layouts.app')

@section('content')
<div class="products-container">
    <!-- Header Section -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-box"></i>
            قائمة المنتجات
        </h1>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card total-products" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-number">{{ $products->count() }}</div>
            <div class="stat-label">إجمالي المنتجات</div>
        </div>

        <div class="stat-card total-quantity" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">{{ number_format($products->sum('quantity')) }}</div>
            <div class="stat-label">إجمالي الكمية</div>
        </div>

        <div class="stat-card total-stock-value" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number">
                {{ number_format($products->sum(function($p){ return ($p->purchase_price ?? 0) * ($p->quantity ?? 0); }), 2) }}
            </div>
            <div class="stat-label">إجمالي قيمة المنتجات</div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="table-container" data-aos="fade-up" data-aos-delay="400">
        <div class="table-header">
            <h2 class="table-title">
                <i class="fas fa-list"></i>
                المنتجات المتاحة
            </h2>
            <div class="search-controls">
                <input type="text" id="product-search-input" class="product-search-select" placeholder="ابحث عن منتج بالاسم..." style="width: 300px;">
                <button type="button" id="search-btn" class="reset-btn">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
                <button type="button" id="reset-search" class="reset-btn">
                    <i class="fas fa-undo"></i>
                    إعادة تعيين
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="products-table" id="productsTable">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-tag"></i>
                            اسم المنتج
                        </th>
                        <th>
                            <i class="fas fa-cubes"></i>
                            الكمية
                        </th>
                        <th>
                            <i class="fas fa-shopping-cart"></i>
                            سعر الشراء
                        </th>
                        <th>
                            <i class="fas fa-money-bill-wave"></i>
                            سعر المبيع
                        </th>
                        <th>
                            <i class="fas fa-cogs"></i>
                            إجراء
                        </th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    @forelse($products as $product)
                        <tr class="product-row" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <td>
                                <div class="product-name">{{ $product->name }}</div>
                            </td>
                            <td>
                                <span class="quantity-badge {{ $product->quantity < 10 ? 'quantity-low' : '' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td>
                                <form action="/invoice/public/products/{{ $product->id }}/update-purchase-price" method="POST" class="price-form purchase-price-form" data-product-id="{{ $product->id }}">
                                    @csrf
                                    <div class="price-input-group">
                                        <input type="number"
                                               name="purchase_price"
                                               value="{{ $product->purchase_price ?? '' }}"
                                               step="0.01"
                                               min="0"
                                               class="price-input purchase-price-input"
                                               placeholder="أدخل السعر"
                                               data-original-value="{{ $product->purchase_price ?? '' }}">
                                        <select class="last-prices-dropdown">
                                            <option value="">آخر الأسعار</option>
                                        </select>
                                        <button type="submit" class="save-btn" disabled>
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <form action="/invoice/public/products/{{ $product->id }}/update-sale-price" method="POST" class="price-form sale-price-form">
                                    @csrf
                                    <div class="price-input-group">
                                        <input type="number"
                                               name="sale_price"
                                               value="{{ $product->sale_price ?? '' }}"
                                               step="0.01"
                                               min="0"
                                               class="price-input sale-price-input"
                                               placeholder="أدخل السعر"
                                               data-original-value="{{ $product->sale_price ?? '' }}">
                                        <span class="currency">ر.س</span>
                                        <button type="submit" class="save-btn" disabled>
                                            <i class="fas fa-save"></i>
                                            حفظ
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div class="empty-text">لا يوجد منتجات حالياً</div>
                                <div class="empty-subtext">قم بإضافة منتجات جديدة لبدء العمل</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage">
        <i class="fas fa-check-circle"></i>
        <span>تم حفظ السعر بنجاح!</span>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">جاري الحفظ...</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/products-list.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/products-list.js') }}"></script>
    <script>
        $(document).ready(function() {
            // حفظ نسخة من الجدول الأصلي
            var originalTableHtml = $('#products-table-body').html();

            // عند الضغط على زر البحث أو Enter في الحقل
            $('#search-btn').on('click', function() {
                searchProducts();
            });
            $('#product-search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    searchProducts();
                }
            });

            // زر إعادة التعيين
            $('#reset-search').on('click', function() {
                $('#product-search-input').val('');
                $('#products-table-body').html(originalTableHtml);
            });

            function searchProducts() {
                var search = $('#product-search-input').val().trim();
                if (!search) return;
                $.ajax({
                    url: '{{ route('products.search') }}',
                    data: { search: search },
                    dataType: 'json',
                    success: function(data) {
                        var rows = '';
                        if (data.data.length === 0) {
                            rows = `<tr><td colspan="5" class="empty-state">
                                <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                <div class="empty-text">لا يوجد منتجات مطابقة</div>
                                <div class="empty-subtext">جرب كلمة بحث أخرى</div>
                            </td></tr>`;
                        } else {
                            data.data.forEach(function(product) {
                                var purchasePrice = product.purchase_price || '';
                                var salePrice = product.sale_price || '';

                                rows += `<tr class="product-row" data-product-id="${product.id}">
                                    <td><div class="product-name">${product.name}</div></td>
                                    <td><span class="quantity-badge ${product.quantity < 10 ? 'quantity-low' : ''}">${product.quantity}</span></td>
                                    <td>
                                        <form action="/products/${product.id}/update-purchase-price" method="POST" class="price-form purchase-price-form" data-product-id="${product.id}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="price-input-group">
                                                <input type="number"
                                                       name="purchase_price"
                                                       value="${purchasePrice}"
                                                       step="0.01"
                                                       min="0"
                                                       class="price-input purchase-price-input"
                                                       placeholder="أدخل السعر"
                                                       data-original-value="${purchasePrice}">
                                                <select class="last-prices-dropdown">
                                                    <option value="">آخر الأسعار</option>
                                                </select>
                                                <button type="submit" class="save-btn" disabled>
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="/products/${product.id}/update-sale-price" method="POST" class="price-form sale-price-form">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="price-input-group">
                                                <input type="number"
                                                       name="sale_price"
                                                       value="${salePrice}"
                                                       step="0.01"
                                                       min="0"
                                                       class="price-input sale-price-input"
                                                       placeholder="أدخل السعر"
                                                       data-original-value="${salePrice}">
                                                <span class="currency">ر.س</span>
                                                <button type="submit" class="save-btn" disabled>
                                                    <i class="fas fa-save"></i>
                                                    حفظ
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn" title="تعديل المنتج">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="حذف المنتج">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                            });
                        }
                                                $('#products-table-body').html(rows);

                        // إعادة تفعيل مراقبة تغيير الأسعار للصفوف الجديدة
                        if (typeof setupPriceInputListeners === 'function') {
                            setupPriceInputListeners();
                        }

                        // إعادة تفعيل نماذج الحفظ للصفوف الجديدة
                        if (typeof setupFormSubmissions === 'function') {
                            setupFormSubmissions();
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء البحث!');
                    }
                });
            }
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', 'إنشاء فاتورة جديدة')

@push('styles')
    <link href="{{ asset('css/invoice-create.css') }}" rel="stylesheet">
@endpush

@push('meta')
    <meta name="user-currency" content="{{ auth()->user()->currency ?? 'SYP' }}">
@endpush

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="invoice-create-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i>
                إنشاء فاتورة جديدة
            </h1>
        </div>

        <form method="POST" action="{{ route('invoices.store') }}" class="invoice-form">
            @csrf

            <div class="form-content">
                <!-- بيانات الفاتورة -->
                <div class="form-section">
                    <div class="form-group">
                        <label class="form-label">رقم الفاتورة</label>
                        <input type="text"
                               name="invoice_number"
                               class="form-input"
                               required
                               value="{{ old('invoice_number', isset($nextNumber) ? $nextNumber : '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">نوع الفاتورة</label>
                        <select name="type" class="form-select" required>
                            <option value="بيع">بيع</option>
                            <option value="شراء">شراء</option>
                            <option value="مردودات شراء">مردودات شراء</option>
                            <option value="مردودات بيع">مردودات بيع</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">حالة الفاتورة</label>
                        <select name="status" class="form-select" required>
                            <option value="مقبوضة">مقبوضة</option>
                            <option value="غير مقبوضة">غير مقبوضة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">العميل</label>
                        <select id="client_id" name="client_id" class="form-select" required></select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">تاريخ الفاتورة</label>
                        <input type="date" name="date" id="invoice-date" class="form-input" required>
                    </div>

                    {{-- <div class="form-group">
                        <label class="form-label">العملة</label>
                        <select name="currency" class="form-select" required>
                            <option value="SYP" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'SYP') ? 'selected' : '' }}>ليرة سورية (SYP)</option>
                            <option value="USD" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'USD') ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                            <option value="EUR" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'EUR') ? 'selected' : '' }}>يورو (EUR)</option>
                            <option value="SAR" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'SAR') ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="JOD" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'JOD') ? 'selected' : '' }}>دينار أردني (JOD)</option>
                            <option value="TRY" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'TRY') ? 'selected' : '' }}>ليرة تركية (TRY)</option>
                            <option value="IQD" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'IQD') ? 'selected' : '' }}>دينار عراقي (IQD)</option>
                            <option value="EGP" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'EGP') ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            <option value="AED" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'AED') ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                            <option value="QAR" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'QAR') ? 'selected' : '' }}>ريال قطري (QAR)</option>
                            <option value="OMR" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'OMR') ? 'selected' : '' }}>ريال عماني (OMR)</option>
                            <option value="KWD" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'KWD') ? 'selected' : '' }}>دينار كويتي (KWD)</option>
                            <option value="GBP" {{ (old('currency', auth()->user()->currency ?? 'SYP') == 'GBP') ? 'selected' : '' }}>جنيه إسترليني (GBP)</option>
                        </select>
                    </div> --}}
                    <div class="form-group">
                        <label class="form-label">العملة المستلمة</label>
                        <select name="received_currency" id="received_currency" class="form-select" required>
                            <option value="SYP" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'SYP') ? 'selected' : '' }}>ليرة سورية (SYP)</option>
                            <option value="SAR" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'SAR') ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="AED" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'AED') ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                            <option value="QAR" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'QAR') ? 'selected' : '' }}>ريال قطري (QAR)</option>
                            <option value="KWD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'KWD') ? 'selected' : '' }}>دينار كويتي (KWD)</option>
                            <option value="BHD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'BHD') ? 'selected' : '' }}>دينار بحريني (BHD)</option>
                            <option value="OMR" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'OMR') ? 'selected' : '' }}>ريال عماني (OMR)</option>
                            <option value="JOD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'JOD') ? 'selected' : '' }}>دينار أردني (JOD)</option>
                            <option value="LBP" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'LBP') ? 'selected' : '' }}>ليرة لبنانية (LBP)</option>
                            <option value="IQD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'IQD') ? 'selected' : '' }}>دينار عراقي (IQD)</option>
                            <option value="EGP" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'EGP') ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            <option value="LYD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'LYD') ? 'selected' : '' }}>دينار ليبي (LYD)</option>
                            <option value="TND" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'TND') ? 'selected' : '' }}>دينار تونسي (TND)</option>
                            <option value="DZD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'DZD') ? 'selected' : '' }}>دينار جزائري (DZD)</option>
                            <option value="MAD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'MAD') ? 'selected' : '' }}>درهم مغربي (MAD)</option>
                            <option value="MRU" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'MRU') ? 'selected' : '' }}>أوقية موريتانية (MRU)</option>
                            <option value="SDG" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'SDG') ? 'selected' : '' }}>جنيه سوداني (SDG)</option>
                            <option value="SOS" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'SOS') ? 'selected' : '' }}>شلن صومالي (SOS)</option>
                            <option value="DJF" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'DJF') ? 'selected' : '' }}>فرنك جيبوتي (DJF)</option>
                            <option value="KMF" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'KMF') ? 'selected' : '' }}>فرنك قمري (KMF)</option>
                            <option value="USD" {{ (old('received_currency', auth()->user()->currency ?? 'SYP') == 'USD') ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        </select>
                    </div>
                    <div class="form-group" id="exchange_rate_group" style="display:none;">
                        <label class="form-label">سعر الصرف مقابل العملة الأساسية (1 عملة مستلمة = ? عملة أساسية)</label>
                        <input type="number" name="exchange_rate" id="exchange_rate" class="form-input" min="0" step="0.0001">
                    </div>
                </div>

                <!-- جدول المنتجات -->
                <div class="products-section">
                    <div class="products-header">
                        <h3>المنتجات</h3>
                        <button type="button" id="add-row" class="btn-add-product">
                            <i class="fas fa-plus"></i>
                            إضافة منتج
                        </button>
                    </div>

                    <div class="table-wrapper">
                        <table class="products-table" id="products-table">
                            <thead>
                                <tr>
                                    <th>اسم المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر (العملة الأساسية)</th>
                                    <th>السعر (العملة المستلمة)</th>
                                    <th>الإجمالي (العملة الأساسية)</th>
                                    <th>الإجمالي (العملة المستلمة)</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- لا تضع أي صف هنا، سيتم إنشاء الصف الأول بالجافاسكريبت -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-actions-row">
                <div>
                    <label for="discount">الحسم (قيمة نقدية)</label>
                    <input type="number" name="discount" id="discount" class="form-input discount-input" min="0" step="0.01" value="0">
                </div>
                <div style="margin-top: 10px;">
                    <label class="font-bold">الإجمالي بعد الحسم:</label>
                    <span id="total-after-discount" style="font-weight:bold;color:green;">0</span>
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    حفظ الفاتورة
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        window.routes = {
            clientsAutocomplete: "{{ route('clients.autocomplete') }}",
            productsAutocomplete: "{{ url('/api/products/autocomplete') }}"
        };
        window.productsList = @json($productsArray);
        window.baseCurrency = @json(auth()->user()->currency ?? 'SYP');

        document.addEventListener('DOMContentLoaded', function() {
            // تعبئة تاريخ اليوم تلقائياً
            var dateInput = document.getElementById('invoice-date');
            if (dateInput && !dateInput.value) {
                var today = new Date();
                var yyyy = today.getFullYear();
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var dd = String(today.getDate()).padStart(2, '0');
                dateInput.value = yyyy + '-' + mm + '-' + dd;
            }

            // منطق العملة المستلمة
            var baseCurrency = document.querySelector('meta[name="user-currency"]')?.getAttribute('content') || window.baseCurrency || 'SYP';
            var receivedCurrency = document.getElementById('received_currency');
            var exchangeRateGroup = document.getElementById('exchange_rate_group');

            // تفعيل المنطق عند تحميل الصفحة لأول مرة
            function toggleExchangeRateField() {
                if (receivedCurrency.value && receivedCurrency.value !== baseCurrency) {
                    exchangeRateGroup.style.display = '';
                } else {
                    exchangeRateGroup.style.display = 'none';
                }
            }
            receivedCurrency.addEventListener('change', toggleExchangeRateField);
            toggleExchangeRateField(); // استدعاء عند التحميل

            // حساب الإجمالي بعد الحسم
            window.updateTotalAfterDiscount = function() {
                let total = 0;
                document.querySelectorAll('#products-table .total-base').forEach(function(span) {
                    total += parseFloat(span.textContent) || 0;
                });
                let discount = parseFloat(document.getElementById('discount').value) || 0;
                let after = total - discount;
                if (after < 0) after = 0;
                document.getElementById('total-after-discount').textContent = after.toFixed(2);
            }

            // مراقبة التغييرات في الجدول
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'subtree') {
                        window.updateTotalAfterDiscount();
                    }
                });
            });

            observer.observe(document.getElementById('products-table'), {
                childList: true,
                subtree: true
            });

            // تحديث عند تغيير الحسم
            document.getElementById('discount').addEventListener('input', window.updateTotalAfterDiscount);

            // تحديث أولي
            window.updateTotalAfterDiscount();
        });
    </script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/invoice-create.js') }}"></script>
@endpush

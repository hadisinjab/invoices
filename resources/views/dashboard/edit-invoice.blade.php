@extends('layouts.app')

@section('title', 'تعديل فاتورة')

@push('styles')
    <link href="{{ asset('css/invoice-create.css') }}" rel="stylesheet">
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
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif
    <div class="invoice-create-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-file-invoice-dollar"></i>
                تعديل فاتورة
            </h1>
        </div>
        <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" class="invoice-form">
            @csrf
            @method('PUT')
            <div class="form-content">
                <!-- بيانات الفاتورة -->
                <div class="form-section">
                    <div class="form-group">
                        <label class="form-label">رقم الفاتورة</label>
                        <input type="text" name="invoice_number" class="form-input" required value="{{ old('invoice_number', $invoice->invoice_number) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نوع الفاتورة</label>
                        <select name="type" class="form-select" required>
                            <option value="بيع" {{ $invoice->type == 'بيع' ? 'selected' : '' }}>بيع</option>
                            <option value="شراء" {{ $invoice->type == 'شراء' ? 'selected' : '' }}>شراء</option>
                            <option value="مردودات شراء" {{ $invoice->type == 'مردودات شراء' ? 'selected' : '' }}>مردودات شراء</option>
                            <option value="مردودات بيع" {{ $invoice->type == 'مردودات بيع' ? 'selected' : '' }}>مردودات بيع</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">حالة الفاتورة</label>
                        <select name="status" class="form-select" required>
                            <option value="مقبوضة" {{ $invoice->status == 'مقبوضة' ? 'selected' : '' }}>مقبوضة</option>
                            <option value="غير مقبوضة" {{ $invoice->status == 'غير مقبوضة' ? 'selected' : '' }}>غير مقبوضة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">العميل</label>
                        <select id="client_id" name="client_id" class="form-select" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">تاريخ الفاتورة</label>
                        <input type="date" name="date" id="invoice-date" class="form-input" required value="{{ old('date', \Carbon\Carbon::parse($invoice->date)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">العملة الأساسية</label>
                        <input type="hidden" name="currency" value="{{ auth()->user()->currency ?? $invoice->currency }}">
                        <input type="text" class="form-input" value="{{ auth()->user()->currency ?? $invoice->currency }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">العملة المستلمة</label>
                        <select name="received_currency" id="received_currency" class="form-select" required>
                            <option value="">اختر العملة المستلمة</option>
                            <option value="SAR" {{ $invoice->received_currency == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                            <option value="AED" {{ $invoice->received_currency == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                            <option value="QAR" {{ $invoice->received_currency == 'QAR' ? 'selected' : '' }}>ريال قطري (QAR)</option>
                            <option value="KWD" {{ $invoice->received_currency == 'KWD' ? 'selected' : '' }}>دينار كويتي (KWD)</option>
                            <option value="BHD" {{ $invoice->received_currency == 'BHD' ? 'selected' : '' }}>دينار بحريني (BHD)</option>
                            <option value="OMR" {{ $invoice->received_currency == 'OMR' ? 'selected' : '' }}>ريال عماني (OMR)</option>
                            <option value="JOD" {{ $invoice->received_currency == 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                            <option value="LBP" {{ $invoice->received_currency == 'LBP' ? 'selected' : '' }}>ليرة لبنانية (LBP)</option>
                            <option value="SYP" {{ $invoice->received_currency == 'SYP' ? 'selected' : '' }}>ليرة سورية (SYP)</option>
                            <option value="IQD" {{ $invoice->received_currency == 'IQD' ? 'selected' : '' }}>دينار عراقي (IQD)</option>
                            <option value="EGP" {{ $invoice->received_currency == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                            <option value="LYD" {{ $invoice->received_currency == 'LYD' ? 'selected' : '' }}>دينار ليبي (LYD)</option>
                            <option value="TND" {{ $invoice->received_currency == 'TND' ? 'selected' : '' }}>دينار تونسي (TND)</option>
                            <option value="DZD" {{ $invoice->received_currency == 'DZD' ? 'selected' : '' }}>دينار جزائري (DZD)</option>
                            <option value="MAD" {{ $invoice->received_currency == 'MAD' ? 'selected' : '' }}>درهم مغربي (MAD)</option>
                            <option value="MRU" {{ $invoice->received_currency == 'MRU' ? 'selected' : '' }}>أوقية موريتانية (MRU)</option>
                            <option value="SDG" {{ $invoice->received_currency == 'SDG' ? 'selected' : '' }}>جنيه سوداني (SDG)</option>
                            <option value="SOS" {{ $invoice->received_currency == 'SOS' ? 'selected' : '' }}>شلن صومالي (SOS)</option>
                            <option value="DJF" {{ $invoice->received_currency == 'DJF' ? 'selected' : '' }}>فرنك جيبوتي (DJF)</option>
                            <option value="KMF" {{ $invoice->received_currency == 'KMF' ? 'selected' : '' }}>فرنك قمري (KMF)</option>
                            <option value="USD" {{ $invoice->received_currency == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        </select>
                    </div>
                    <div class="form-group" id="exchange_rate_group" style="display:{{ ($invoice->received_currency && $invoice->received_currency !== ($invoice->currency ?? (auth()->user()->currency ?? 'SYP'))) ? '' : 'none' }};">
                        <label class="form-label">سعر الصرف مقابل العملة الأساسية (1 عملة مستلمة = ? عملة أساسية)</label>
                        <input type="number" name="exchange_rate" id="exchange_rate" class="form-input" min="0" step="0.0001" value="{{ $invoice->exchange_rate }}">
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
                                    <th>السعر ({{ $invoice->currency ?? (auth()->user()->currency ?? 'SYP') }})</th>
                                    <th>السعر ({{ $invoice->received_currency ?? '-' }})</th>
                                    <th>الإجمالي ({{ $invoice->currency ?? (auth()->user()->currency ?? 'SYP') }})</th>
                                    <th>الإجمالي ({{ $invoice->received_currency ?? '-' }})</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                 {{-- Rows will be added dynamically by invoice-edit.js --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-actions-row">
                <div>
                    <label for="discount">الحسم (قيمة نقدية)</label>
                    <input type="number" name="حسم" id="discount" class="form-input discount-input" min="0" step="0.01" value="{{ old('حسم', $invoice->حسم ?? 0) }}">
                </div>
                <div style="margin-top: 10px;">
                    <label class="font-bold">الإجمالي بعد الحسم:</label>
                    <span id="total-after-discount" style="font-weight:bold;color:green;">0</span>
                </div>
                <button type="submit" class="btn-save">
                    <i class="fas fa-edit"></i>
                    تعديل الفاتورة
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
        window.oldInvoiceItems = @json($oldInvoiceItems);

        document.addEventListener('DOMContentLoaded', function() {
            var baseCurrency = window.baseCurrency;
            var receivedCurrency = document.getElementById('received_currency');
            var exchangeRateGroup = document.getElementById('exchange_rate_group');
            receivedCurrency.addEventListener('change', function() {
                if (this.value && this.value !== baseCurrency) {
                    exchangeRateGroup.style.display = '';
                } else {
                    exchangeRateGroup.style.display = 'none';
                }
            });
                          document.getElementById('discount').addEventListener('input', function() {
                 if (typeof window.updateTotalAfterDiscount === 'function') {
                     window.updateTotalAfterDiscount();
                 }
             });

             // تحديث الإجمالي عند تحميل الصفحة
             setTimeout(function() {
                 if (typeof window.updateTotalAfterDiscount === 'function') {
                     window.updateTotalAfterDiscount();
                 }
             }, 100);
         });
    </script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/invoice-edit.js') }}"></script>
@endpush

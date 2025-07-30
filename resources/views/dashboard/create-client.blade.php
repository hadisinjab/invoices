@extends('layouts.app')
@section('title', 'إضافة عميل جديد - نظام الفواتير')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/create-client.css') }}">
@endpush

@section('content')
    <!-- شريط التحميل -->
    <div class="loading-bar" id="loadingBar"></div>

    <!-- الحاوي الرئيسي -->
    <div class="main-container">
        <!-- الهيدر -->
        <header class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <a href="{{ route('clients.index') }}" class="back-btn">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة للعملاء</span>
                    </a>
                </div>
                <div class="header-center">
                    <h1 class="page-title">
                        <i class="fas fa-user-plus"></i>
                        إضافة عميل جديد
                    </h1>
                </div>
                <div class="header-right">
                    <div class="breadcrumb">
                        <span>العملاء</span>
                        <i class="fas fa-chevron-left"></i>
                        <span>إضافة عميل</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- المحتوى الرئيسي -->
        <main class="main-content">
            <div class="form-container">
                <div class="form-card">
                    <!-- تقدم النموذج -->
                    <div class="progress-bar">
                        <div class="progress-step active" data-step="1">
                            <div class="step-circle">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>المعلومات الأساسية</span>
                        </div>
                        <div class="progress-step" data-step="2">
                            <div class="step-circle">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span>العنوان</span>
                        </div>
                        <div class="progress-step" data-step="3">
                            <div class="step-circle">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>المراجعة</span>
                        </div>
                    </div>

                    <!-- النموذج -->
                    <form action="{{ route('clients.store') }}" method="POST" id="clientForm" class="client-form">
                        @csrf

                        <!-- الخطوة الأولى: المعلومات الأساسية -->
                        <div class="form-step active" data-step="1">
                            <div class="step-header">
                                <h2>
                                    <i class="fas fa-user"></i>
                                    المعلومات الأساسية
                                </h2>
                                <p>أدخل البيانات الأساسية للعميل</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name" class="form-label required">
                                        <i class="fas fa-user"></i>
                                        الاسم الكامل
                                    </label>
                                    <input type="text" name="name" id="name" class="form-input" required value="{{ old('name') }}" placeholder="أدخل الاسم الكامل">
                                    <div class="error-message" id="name-error"></div>
                                    @error('name')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        البريد الإلكتروني
                                    </label>
                                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" placeholder="example@domain.com">
                                    <div class="error-message" id="email-error"></div>
                                    @error('email')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group full-width">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i>
                                        رقم الهاتف
                                    </label>
                                    <input type="tel" name="phone" id="phone" class="form-input" value="{{ old('phone') }}" placeholder="+966 50 123 4567">
                                    <div class="error-message" id="phone-error"></div>
                                    @error('phone')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group full-width">
                                    <label for="company" class="form-label">
                                        <i class="fas fa-building"></i>
                                        الشركة (اختياري)
                                    </label>
                                    <input type="text" name="company" id="company" class="form-input" value="{{ old('company') }}" placeholder="اسم الشركة">
                                    <div class="error-message" id="company-error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- الخطوة الثانية: العنوان -->
                        <div class="form-step" data-step="2">
                            <div class="step-header">
                                <h2>
                                    <i class="fas fa-map-marker-alt"></i>
                                    معلومات العنوان
                                </h2>
                                <p>أدخل عنوان العميل التفصيلي</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-home"></i>
                                        العنوان
                                    </label>
                                    <input type="text" name="address" id="address" class="form-input" value="{{ old('address') }}" placeholder="الشارع، الحي، رقم المبنى">
                                    <div class="error-message" id="address-error"></div>
                                    @error('address')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="city" class="form-label">
                                        <i class="fas fa-city"></i>
                                        المدينة
                                    </label>
                                    <input type="text" name="city" id="city" class="form-input" value="{{ old('city') }}" placeholder="الرياض، جدة، الدمام...">
                                    <div class="error-message" id="city-error"></div>
                                    @error('city')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="country" class="form-label">
                                        <i class="fas fa-globe"></i>
                                        الدولة
                                    </label>
                                    <select name="country" id="country" class="form-input">
                                        <option value="">اختر الدولة</option>
                                        <option value="الإمارات العربية المتحدة" {{ old('country') == 'الإمارات العربية المتحدة' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                        <option value="البحرين" {{ old('country') == 'البحرين' ? 'selected' : '' }}>البحرين</option>
                                        <option value="تونس" {{ old('country') == 'تونس' ? 'selected' : '' }}>تونس</option>
                                        <option value="الجزائر" {{ old('country') == 'الجزائر' ? 'selected' : '' }}>الجزائر</option>
                                        <option value="جيبوتي" {{ old('country') == 'جيبوتي' ? 'selected' : '' }}>جيبوتي</option>
                                        <option value="السعودية" {{ old('country') == 'السعودية' ? 'selected' : '' }}>السعودية</option>
                                        <option value="السودان" {{ old('country') == 'السودان' ? 'selected' : '' }}>السودان</option>
                                        <option value="سوريا" {{ old('country') == 'سوريا' ? 'selected' : '' }}>سوريا</option>
                                        <option value="الصومال" {{ old('country') == 'الصومال' ? 'selected' : '' }}>الصومال</option>
                                        <option value="العراق" {{ old('country') == 'العراق' ? 'selected' : '' }}>العراق</option>
                                        <option value="عمان" {{ old('country') == 'عمان' ? 'selected' : '' }}>عمان</option>
                                        <option value="فلسطين" {{ old('country') == 'فلسطين' ? 'selected' : '' }}>فلسطين</option>
                                        <option value="قطر" {{ old('country') == 'قطر' ? 'selected' : '' }}>قطر</option>
                                        <option value="الكويت" {{ old('country') == 'الكويت' ? 'selected' : '' }}>الكويت</option>
                                        <option value="لبنان" {{ old('country') == 'لبنان' ? 'selected' : '' }}>لبنان</option>
                                        <option value="ليبيا" {{ old('country') == 'ليبيا' ? 'selected' : '' }}>ليبيا</option>
                                        <option value="مصر" {{ old('country') == 'مصر' ? 'selected' : '' }}>مصر</option>
                                        <option value="المغرب" {{ old('country') == 'المغرب' ? 'selected' : '' }}>المغرب</option>
                                        <option value="موريتانيا" {{ old('country') == 'موريتانيا' ? 'selected' : '' }}>موريتانيا</option>
                                        <option value="الأردن" {{ old('country') == 'الأردن' ? 'selected' : '' }}>الأردن</option>
                                        <option value="اليمن" {{ old('country') == 'اليمن' ? 'selected' : '' }}>اليمن</option>
                                        <option value="أخرى" {{ old('country') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                    <div class="error-message" id="country-error"></div>
                                    @error('country')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="postal_code" class="form-label">
                                        <i class="fas fa-mail-bulk"></i>
                                        الرمز البريدي
                                    </label>
                                    <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code') }}" placeholder="12345">
                                    <div class="error-message" id="postal_code-error"></div>
                                </div>
                            </div>
                        </div>

                        <!-- الخطوة الثالثة: المراجعة -->
                        <div class="form-step" data-step="3">
                            <div class="step-header">
                                <h2>
                                    <i class="fas fa-check-circle"></i>
                                    مراجعة البيانات
                                </h2>
                                <p>تأكد من صحة البيانات المدخلة</p>
                            </div>

                            <div class="review-section">
                                <div class="review-card">
                                    <h3>
                                        <i class="fas fa-user"></i>
                                        المعلومات الأساسية
                                    </h3>
                                    <div class="review-item">
                                        <span class="review-label">الاسم:</span>
                                        <span class="review-value" id="review-name">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">البريد الإلكتروني:</span>
                                        <span class="review-value" id="review-email">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الهاتف:</span>
                                        <span class="review-value" id="review-phone">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الشركة:</span>
                                        <span class="review-value" id="review-company">-</span>
                                    </div>
                                </div>

                                <div class="review-card">
                                    <h3>
                                        <i class="fas fa-map-marker-alt"></i>
                                        العنوان
                                    </h3>
                                    <div class="review-item">
                                        <span class="review-label">العنوان:</span>
                                        <span class="review-value" id="review-address">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">المدينة:</span>
                                        <span class="review-value" id="review-city">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الدولة:</span>
                                        <span class="review-value" id="review-country">-</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الرمز البريدي:</span>
                                        <span class="review-value" id="review-postal_code">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار التنقل -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                                <i class="fas fa-arrow-right"></i>
                                السابق
                            </button>

                            <div class="actions-right">
                                <button type="button" class="btn btn-primary" id="nextBtn">
                                    التالي
                                    <i class="fas fa-arrow-left"></i>
                                </button>

                                <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                    <i class="fas fa-save"></i>
                                    حفظ العميل
                                </button>

                                <a href="{{ route('clients.index') }}" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- إشعارات النجاح والخطأ -->
    <div class="notification" id="notification">
        <div class="notification-content">
            <i class="notification-icon"></i>
            <span class="notification-message"></span>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/create-client.js') }}"></script>
@endpush

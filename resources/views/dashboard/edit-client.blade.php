@extends('layouts.app')
@section('title', 'تعديل بيانات العميل - نظام الفواتير')

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
                        <i class="fas fa-user-edit"></i>
                        تعديل بيانات العميل
                    </h1>
                </div>
                <div class="header-right">
                    <div class="breadcrumb">
                        <span>العملاء</span>
                        <i class="fas fa-chevron-left"></i>
                        <span>تعديل العميل</span>
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
                        <div class="progress-step active" data-step="2">
                            <div class="step-circle">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span>العنوان</span>
                        </div>
                        <div class="progress-step active" data-step="3">
                            <div class="step-circle">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>المراجعة</span>
                        </div>
                    </div>

                    <!-- النموذج -->
                    <form action="{{ route('clients.update', $client->id) }}" method="POST" id="clientForm" class="client-form">
                        @csrf
                        @method('PUT')

                        <!-- الخطوة الأولى: المعلومات الأساسية -->
                        <div class="form-step active" data-step="1">
                            <div class="step-header">
                                <h2>
                                    <i class="fas fa-user"></i>
                                    المعلومات الأساسية
                                </h2>
                                <p>تعديل البيانات الأساسية للعميل</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name" class="form-label required">
                                        <i class="fas fa-user"></i>
                                        الاسم الكامل
                                    </label>
                                    <input type="text" name="name" id="name" class="form-input" required value="{{ old('name', $client->name) }}" placeholder="أدخل الاسم الكامل">
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
                                    <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $client->email) }}" placeholder="example@domain.com">
                                    <div class="error-message" id="email-error"></div>
                                    @error('email')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i>
                                        رقم الهاتف
                                    </label>
                                    <input type="tel" name="phone" id="phone" class="form-input" value="{{ old('phone', $client->phone) }}" placeholder="+966 50 123 4567">
                                    <div class="error-message" id="phone-error"></div>
                                    @error('phone')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="company" class="form-label">
                                        <i class="fas fa-building"></i>
                                        اسم الشركة
                                    </label>
                                    <input type="text" name="company" id="company" class="form-input" value="{{ old('company', $client->company) }}" placeholder="اسم الشركة (اختياري)">
                                    <div class="error-message" id="company-error"></div>
                                    @error('company')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- الخطوة الثانية: العنوان -->
                        <div class="form-step active" data-step="2">
                            <div class="step-header">
                                <h2>
                                    <i class="fas fa-map-marker-alt"></i>
                                    العنوان
                                </h2>
                                <p>تعديل عنوان العميل</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="address" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i>
                                        العنوان التفصيلي
                                    </label>
                                    <textarea name="address" id="address" class="form-input" rows="3" placeholder="أدخل العنوان التفصيلي">{{ old('address', $client->address) }}</textarea>
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
                                    <input type="text" name="city" id="city" class="form-input" value="{{ old('city', $client->city) }}" placeholder="أدخل اسم المدينة">
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
                                        <option value="الإمارات العربية المتحدة" {{ old('country', $client->country) == 'الإمارات العربية المتحدة' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                        <option value="البحرين" {{ old('country', $client->country) == 'البحرين' ? 'selected' : '' }}>البحرين</option>
                                        <option value="تونس" {{ old('country', $client->country) == 'تونس' ? 'selected' : '' }}>تونس</option>
                                        <option value="الجزائر" {{ old('country', $client->country) == 'الجزائر' ? 'selected' : '' }}>الجزائر</option>
                                        <option value="جيبوتي" {{ old('country', $client->country) == 'جيبوتي' ? 'selected' : '' }}>جيبوتي</option>
                                        <option value="السعودية" {{ old('country', $client->country) == 'السعودية' ? 'selected' : '' }}>السعودية</option>
                                        <option value="السودان" {{ old('country', $client->country) == 'السودان' ? 'selected' : '' }}>السودان</option>
                                        <option value="سوريا" {{ old('country', $client->country) == 'سوريا' ? 'selected' : '' }}>سوريا</option>
                                        <option value="الصومال" {{ old('country', $client->country) == 'الصومال' ? 'selected' : '' }}>الصومال</option>
                                        <option value="العراق" {{ old('country', $client->country) == 'العراق' ? 'selected' : '' }}>العراق</option>
                                        <option value="عمان" {{ old('country', $client->country) == 'عمان' ? 'selected' : '' }}>عمان</option>
                                        <option value="فلسطين" {{ old('country', $client->country) == 'فلسطين' ? 'selected' : '' }}>فلسطين</option>
                                        <option value="قطر" {{ old('country', $client->country) == 'قطر' ? 'selected' : '' }}>قطر</option>
                                        <option value="الكويت" {{ old('country', $client->country) == 'الكويت' ? 'selected' : '' }}>الكويت</option>
                                        <option value="لبنان" {{ old('country', $client->country) == 'لبنان' ? 'selected' : '' }}>لبنان</option>
                                        <option value="ليبيا" {{ old('country', $client->country) == 'ليبيا' ? 'selected' : '' }}>ليبيا</option>
                                        <option value="مصر" {{ old('country', $client->country) == 'مصر' ? 'selected' : '' }}>مصر</option>
                                        <option value="المغرب" {{ old('country', $client->country) == 'المغرب' ? 'selected' : '' }}>المغرب</option>
                                        <option value="موريتانيا" {{ old('country', $client->country) == 'موريتانيا' ? 'selected' : '' }}>موريتانيا</option>
                                        <option value="الأردن" {{ old('country', $client->country) == 'الأردن' ? 'selected' : '' }}>الأردن</option>
                                        <option value="اليمن" {{ old('country', $client->country) == 'اليمن' ? 'selected' : '' }}>اليمن</option>
                                        <option value="أخرى" {{ old('country', $client->country) == 'أخرى' ? 'selected' : '' }}>أخرى</option>
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
                                    <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $client->postal_code) }}" placeholder="12345">
                                    <div class="error-message" id="postal_code-error"></div>
                                    @error('postal_code')
                                        <div class="error-message show">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- الخطوة الثالثة: المراجعة -->
                        <div class="form-step active" data-step="3">
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
                                        <span class="review-value" id="review-name">{{ $client->name }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">البريد الإلكتروني:</span>
                                        <span class="review-value" id="review-email">{{ $client->email ?? '-' }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الهاتف:</span>
                                        <span class="review-value" id="review-phone">{{ $client->phone ?? '-' }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الشركة:</span>
                                        <span class="review-value" id="review-company">{{ $client->company ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="review-card">
                                    <h3>
                                        <i class="fas fa-map-marker-alt"></i>
                                        العنوان
                                    </h3>
                                    <div class="review-item">
                                        <span class="review-label">العنوان:</span>
                                        <span class="review-value" id="review-address">{{ $client->address ?? '-' }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">المدينة:</span>
                                        <span class="review-value" id="review-city">{{ $client->city ?? '-' }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الدولة:</span>
                                        <span class="review-value" id="review-country">{{ $client->country ?? '-' }}</span>
                                    </div>
                                    <div class="review-item">
                                        <span class="review-label">الرمز البريدي:</span>
                                        <span class="review-value" id="review-postal_code">{{ $client->postal_code ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار التنقل -->
                        <div class="form-actions">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        // تحديث مراجعة البيانات عند تغيير الحقول
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('clientForm');
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                input.addEventListener('input', updateReview);
                input.addEventListener('change', updateReview);
            });

            function updateReview() {
                const name = document.getElementById('name').value || '-';
                const email = document.getElementById('email').value || '-';
                const phone = document.getElementById('phone').value || '-';
                const company = document.getElementById('company').value || '-';
                const address = document.getElementById('address').value || '-';
                const city = document.getElementById('city').value || '-';
                const country = document.getElementById('country').value || '-';
                const postal_code = document.getElementById('postal_code').value || '-';

                document.getElementById('review-name').textContent = name;
                document.getElementById('review-email').textContent = email;
                document.getElementById('review-phone').textContent = phone;
                document.getElementById('review-company').textContent = company;
                document.getElementById('review-address').textContent = address;
                document.getElementById('review-city').textContent = city;
                document.getElementById('review-country').textContent = country;
                document.getElementById('review-postal_code').textContent = postal_code;
            }
        });
    </script>
@endpush

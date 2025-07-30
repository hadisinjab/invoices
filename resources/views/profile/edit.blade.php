@extends('layouts.app')
@section('title', 'تفاصيل الحساب')

@push('styles')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    .currency-card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    .modal-overlay {
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
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
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    .stagger-1 { animation-delay: 0.1s; }
    .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; }
    .stagger-4 { animation-delay: 0.4s; }

    .profile-section {
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        box-shadow: 20px 20px 60px #d9d9d9, -20px -20px 60px #ffffff;
    }

    .dark .profile-section {
        background: linear-gradient(145deg, #374151, #4b5563);
        box-shadow: 20px 20px 60px #2d3748, -20px -20px 60px #4a5568;
    }

    .section-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 1rem;
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    }
</style>
@endpush

@push('scripts')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="gradient-bg">
    <div class="py-12" x-data="profileManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Header Section with Currency Selection -->
            <div class="text-center mb-12 fade-in-up">
                <h1 class="text-4xl font-bold text-white mb-4">
                    <i class="fas fa-user-cog ml-3"></i>
                    إعدادات الحساب
                </h1>
                <p class="text-xl text-gray-200">إدارة معلوماتك الشخصية وإعدادات الأمان</p>
            </div>

            <!-- Currency Selection Card -->
            <div class="glass-effect rounded-2xl p-8 shadow-2xl card-hover fade-in-up stagger-1">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="section-icon">
                            <i class="fas fa-coins text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">العملة المفضلة</h2>
                            <p class="text-gray-300">اختر العملة التي تفضل عرض الأسعار بها</p>
                        </div>
                    </div>
                    <button
                        @click="showCurrencyModal = true"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-full font-semibold shadow-lg transform hover:scale-105 transition-all duration-300"
                    >
                        <i class="fas fa-edit ml-2"></i>
                        تغيير العملة
                    </button>
                </div>

                <!-- Selected Currency Display -->
                <div class="bg-white bg-opacity-20 rounded-xl p-6 border border-white border-opacity-30">
                    <div class="flex items-center justify-center space-x-6 rtl:space-x-reverse">
                        <div class="text-center">
                            <div class="text-4xl mb-3" x-text="getCurrencyFlag(selectedCurrency)"></div>
                            <div class="text-white font-bold text-xl" x-text="getCurrencyName(selectedCurrency)"></div>
                            <div class="text-gray-200 text-sm font-medium" x-text="selectedCurrency"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Section -->
            <div class="profile-section dark:bg-gray-800 rounded-2xl shadow-2xl card-hover fade-in-up stagger-2">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="section-icon">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">معلومات الملف الشخصي</h2>
                            <p class="text-gray-600 dark:text-gray-400">قم بتحديث معلومات ملفك الشخصي وعنوان البريد الإلكتروني</p>
                        </div>
                    </div>
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="profile-section dark:bg-gray-800 rounded-2xl shadow-2xl card-hover fade-in-up stagger-3">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="section-icon">
                            <i class="fas fa-lock text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تحديث كلمة المرور</h2>
                            <p class="text-gray-600 dark:text-gray-400">تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمناً</p>
                        </div>
                    </div>
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="profile-section dark:bg-gray-800 rounded-2xl shadow-2xl card-hover fade-in-up stagger-4 border-2 border-red-200 dark:border-red-800">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        <div class="section-icon bg-gradient-to-r from-red-500 to-red-700">
                            <i class="fas fa-user-times text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-red-600 dark:text-red-400">حذف الحساب</h2>
                            <p class="text-gray-600 dark:text-gray-400">حذف حسابك نهائياً مع جميع البيانات المرتبطة به</p>
                        </div>
                    </div>
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Currency Selection Modal -->
            <div x-show="showCurrencyModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto modal-overlay flex items-center justify-center p-4"
                 @click.away="showCurrencyModal = false"
                 style="display: none;">

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-96 overflow-y-auto"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="scale-95"
                     x-transition:enter-end="scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="scale-100"
                     x-transition:leave-end="scale-95"
                     @click.stop>

                    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="fas fa-coins ml-3 text-yellow-500"></i>
                                اختر العملة المفضلة
                            </h3>
                            <button @click="showCurrencyModal = false"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl">
                                ×
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            <template x-for="currency in currencies" :key="currency.code">
                                <div @click="selectedCurrency = currency.code; showCurrencyModal = false; saveCurrencyPreference(currency.code)"
                                     class="currency-card cursor-pointer bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 text-center shadow-md hover:shadow-lg border-2 border-transparent hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300"
                                     :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900': selectedCurrency === currency.code }">
                                    <div class="text-3xl mb-2" x-text="currency.flag"></div>
                                    <div class="font-semibold text-gray-800 dark:text-white text-sm" x-text="currency.name"></div>
                                    <div class="text-gray-600 dark:text-gray-400 text-xs" x-text="currency.code"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- فورم مخفي لتحديث العملة -->
<form id="currency-form" method="POST" action="{{ route('profile.currency.update') }}">
    @csrf
    @method('PATCH')
    <input type="hidden" name="currency" id="currency-input" value="{{ auth()->user()->currency ?? 'SAR' }}">
</form>

<script>
function profileManager() {
    return {
        selectedCurrency: localStorage.getItem('preferredCurrency') || '{{ auth()->user()->currency ?? 'SAR' }}',
        showCurrencyModal: false,

        currencies: [
            // العملات العربية
            { code: 'SAR', name: 'ريال سعودي', flag: '🇸🇦' },
            { code: 'AED', name: 'درهم إماراتي', flag: '🇦🇪' },
            { code: 'QAR', name: 'ريال قطري', flag: '🇶🇦' },
            { code: 'KWD', name: 'دينار كويتي', flag: '🇰🇼' },
            { code: 'BHD', name: 'دينار بحريني', flag: '🇧🇭' },
            { code: 'OMR', name: 'ريال عماني', flag: '🇴🇲' },
            { code: 'JOD', name: 'دينار أردني', flag: '🇯🇴' },
            { code: 'LBP', name: 'ليرة لبنانية', flag: '🇱🇧' },
            { code: 'SYP', name: 'ليرة سورية', flag: '🇸🇾' },
            { code: 'IQD', name: 'دينار عراقي', flag: '🇮🇶' },
            { code: 'EGP', name: 'جنيه مصري', flag: '🇪🇬' },
            { code: 'LYD', name: 'دينار ليبي', flag: '🇱🇾' },
            { code: 'TND', name: 'دينار تونسي', flag: '🇹🇳' },
            { code: 'DZD', name: 'دينار جزائري', flag: '🇩🇿' },
            { code: 'MAD', name: 'درهم مغربي', flag: '🇲🇦' },
            { code: 'MRU', name: 'أوقية موريتانية', flag: '🇲🇷' },
            { code: 'SDG', name: 'جنيه سوداني', flag: '🇸🇩' },
            { code: 'SOS', name: 'شلن صومالي', flag: '🇸🇴' },
            { code: 'DJF', name: 'فرنك جيبوتي', flag: '🇩🇯' },
            { code: 'KMF', name: 'فرنك قمري', flag: '🇰🇲' },
            // الدولار الأمريكي
            { code: 'USD', name: 'دولار أمريكي', flag: '🇺🇸' }
        ],

        getCurrencyName(code) {
            const currency = this.currencies.find(c => c.code === code);
            return currency ? currency.name : code;
        },

        getCurrencyFlag(code) {
            const currency = this.currencies.find(c => c.code === code);
            return currency ? currency.flag : '💰';
        },

        saveCurrencyPreference(currency) {
            localStorage.setItem('preferredCurrency', currency);
            document.getElementById('currency-input').value = currency;
            document.getElementById('currency-form').submit();
        }
    }
}
</script>
@endsection

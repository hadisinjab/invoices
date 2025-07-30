@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page_title', 'مرحبا بك 👋')

@section('content')
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- إجمالي الفواتير -->
        <div class="stats-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold mb-2 counter" data-target="{{ $summary['count'] }}">0</div>
                    <div class="text-blue-100 text-sm font-medium">إجمالي الفواتير</div>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-blue-100 text-sm">
                <i class="fas fa-trending-up mr-2"></i>
                <span>
                    @if($summary['invoices_percent'] >= 0)
                        <span class="text-green-200 font-semibold" title="زيادة في النشاط">+{{ $summary['invoices_percent'] }}%</span>
                    @else
                        <span class="text-red-200 font-semibold" title="انخفاض في النشاط">{{ $summary['invoices_percent'] }}%</span>
                    @endif
                    <span class="text-blue-100"> من الأسبوع الماضي</span>
                    @if($summary['invoices_percent'] != 0)
                        <span class="text-blue-100 text-xs block mt-1">
                            @if($summary['invoices_percent'] > 0)
                                📈 تحسن في الأداء
                            @else
                                📉 انخفاض في الأداء
                            @endif
                        </span>
                    @endif
                </span>
            </div>
        </div>
        <!-- الفواتير المقبوضة -->
        <div class="stats-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold mb-2 counter" data-target="{{ $summary['paid'] }}">0</div>
                    <div class="text-green-100 text-sm font-medium">فواتير مقبوضة</div>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-100 text-sm">
                <i class="fas fa-arrow-up mr-2"></i>
                <span>
                    @if($summary['paid_percent'] >= 0)
                        <span class="text-green-200 font-semibold" title="زيادة في الفواتير المقبوضة">+{{ $summary['paid_percent'] }}%</span>
                    @else
                        <span class="text-red-200 font-semibold" title="انخفاض في الفواتير المقبوضة">{{ $summary['paid_percent'] }}%</span>
                    @endif
                    <span class="text-green-100"> من الأسبوع الماضي</span>
                    @if($summary['paid_percent'] != 0)
                        <span class="text-green-100 text-xs block mt-1">
                            @if($summary['paid_percent'] > 0)
                                ✅ تحسن في التحصيل
                            @else
                                ⚠️ انخفاض في التحصيل
                            @endif
                        </span>
                    @endif
                </span>
            </div>
        </div>
        <!-- الفواتير غير المقبوضة -->
        <div class="stats-card bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold mb-2 counter" data-target="{{ $summary['unpaid'] }}">0</div>
                    <div class="text-red-100 text-sm font-medium">فواتير غير مقبوضة</div>
                </div>
                <div class="text-4xl opacity-80">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-red-100 text-sm">
                <i class="fas fa-arrow-down mr-2"></i>
                <span>
                    @if($summary['unpaid_percent'] >= 0)
                        <span class="text-green-200 font-semibold" title="انخفاض في الفواتير غير المقبوضة">+{{ $summary['unpaid_percent'] }}%</span>
                    @else
                        <span class="text-red-200 font-semibold" title="زيادة في الفواتير غير المقبوضة">{{ $summary['unpaid_percent'] }}%</span>
                    @endif
                    <span class="text-red-100"> من الأسبوع الماضي</span>
                    @if($summary['unpaid_percent'] != 0)
                        <span class="text-red-100 text-xs block mt-1">
                            @if($summary['unpaid_percent'] < 0)
                                🎉 تحسن في التحصيل
                            @else
                                ⚠️ زيادة في الفواتير المعلقة
                            @endif
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <!-- الأرباح والإحصائيات المتقدمة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
        @php
            $goal = $summary['profit_last_month'] * 1.2;
            $progress = $goal > 0 ? min(100, round(($summary['profit_last_month'] / $goal) * 100)) : 0;
        @endphp
        <!-- كرت الأرباح الشهرية -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 mb-6">
            <div class="flex flex-col items-center justify-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">الأرباح الشهرية</h3>
                <div class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 mb-2 profit-counter" data-target="{{ $summary['profit_last_month'] }}">
                    {{ $summary['profit_last_month'] }}
                </div>
                <div class="text-gray-600 text-sm mb-2">
                    {{ $currency }}
                </div>
            </div>
            <div class="mt-8 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-600">هدف الشهر</span>
                    <span class="font-semibold text-purple-600">{{ $goal }}</span>
                </div>
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full progress-bar" style="width: 0%" data-progress="{{ $progress }}"></div>
                </div>
            </div>
        </div>
        <!-- كرت الإحصائيات المتقدمة -->
        <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-xl font-bold text-gray-800 mb-6">إحصائيات متقدمة</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-3">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">أكثر العملاء شراءً</div>
                            <div class="text-sm text-gray-600">{{ $summary['top_selling_client'] ?? 'لا يوجد' }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-blue-600">{{ $summary['top_selling_client_total'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">{{ $currency }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-3">
                            <i class="fas fa-store text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">أكثر المورّدين</div>
                            <div class="text-sm text-gray-600">{{ $summary['top_supplier'] ?? 'لا يوجد' }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-purple-600">{{ $summary['top_supplier_total'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">{{ $currency }}</div>
                    </div>
                </div>
                <!-- أكثر المنتجات مبيعاً -->
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-3">
                            <i class="fas fa-cube text-green-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">أكثر المنتجات مبيعاً</div>
                            <ul class="text-sm text-gray-600 list-disc pr-4">
                                @forelse($summary['top_products'] as $name => $qty)
                                    <li>{{ $name }} <span class="font-bold text-green-700">({{ $qty }})</span></li>
                                @empty
                                    <li>لا يوجد بيانات</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- المنتجات المنتهية -->
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-xl">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full mr-3">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">المنتجات المنتهية</div>
                            <ul class="text-sm text-gray-600 list-disc pr-4">
                                @forelse($summary['out_of_stock'] as $name)
                                    <li>{{ $name }}</li>
                                @empty
                                    <li>لا يوجد منتجات منتهية</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- الرسم البياني -->
    <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">مبيعات آخر 30 يوم</h3>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="chart-btn active bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:shadow-lg" data-chart="sales">
                    <i class="fas fa-chart-line mr-2"></i>
                    المبيعات
                </button>
                <button class="chart-btn bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-gray-200 hover:shadow-lg" data-chart="profit">
                    <i class="fas fa-chart-area mr-2"></i>
                    الأرباح
                </button>
            </div>
        </div>
        <div class="relative" style="height: 300px;">
            <canvas id="salesChart"></canvas>
            <!-- مؤشر تحميل -->
            <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 rounded-xl">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                    <p class="text-gray-600 text-sm">جاري تحميل البيانات...</p>
                </div>
            </div>
        </div>
    </div>
    <!-- جدول الفواتير حسب النوع -->
    <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">تفصيل الفواتير حسب النوع والعملة المستلمة</h3>
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                تقرير شامل
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <th class="text-right p-4 font-bold text-gray-700 rounded-tr-xl">نوع الفاتورة</th>
                        @foreach($summary['all_currencies'] as $currency)
                            <th class="text-center p-4 font-bold text-gray-700 {{ $loop->last ? 'rounded-tl-xl' : '' }}">{{ $currency }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary['totals_by_type_and_currency'] as $type => $totals)
                        <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 table-row">
                            <td class="p-4 font-semibold text-gray-800">{{ $type }}</td>
                            @foreach($summary['all_currencies'] as $currency)
                                <td class="p-4 text-center font-medium text-gray-700">
                                    <span class="inline-block bg-gradient-to-r from-blue-100 to-indigo-100 px-3 py-1 rounded-full text-sm">
                                        {{ $totals[$currency] ?? 0 }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- أزرار الإجراءات السريعة -->
    <div class="fixed bottom-6 left-6 space-y-3 z-50">
        <a href="{{ route('invoices.create') }}" class="fab-btn bg-gradient-to-r from-blue-500 to-blue-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-plus text-xl"></i>
        </a>
        <a href="{{ route('clients.create') }}" class="fab-btn bg-gradient-to-r from-green-500 to-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-user-plus text-xl"></i>
        </a>
        <a href="{{ route('cash-box.index') }}" class="fab-btn bg-gradient-to-r from-yellow-500 to-yellow-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-cash-register text-xl"></i>
        </a>
    </div>
    @push('scripts')
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأثير العد التدريجي للأرقام
            function animateCounter(element, target, duration = 2000) {
                let start = 0;
                const increment = target / (duration / 16);
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= target) {
                        element.textContent = target;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(start);
                    }
                }, 16);
            }
            // تطبيق العد التدريجي على جميع العدادات
            document.querySelectorAll('.counter').forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                animateCounter(counter, target);
            });
            // عداد الأرباح بتنسيق خاص
            const profitCounter = document.querySelector('.profit-counter');
            if (profitCounter) {
                const target = parseFloat(profitCounter.getAttribute('data-target'));
                let start = 0;
                const increment = target / 120;
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= target) {
                        profitCounter.textContent = target;
                        clearInterval(timer);
                    } else {
                        profitCounter.textContent = Math.floor(start);
                    }
                }, 16);
            }
            // تأثير شريط التقدم
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                const targetProgress = parseInt(progressBar.getAttribute('data-progress'));
                setTimeout(() => {
                    progressBar.style.width = targetProgress + '%';
                }, 500);
            }
            // تأثير الظهور التدريجي للبطاقات
            const cards = document.querySelectorAll('.stats-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });

            // تحسين عرض النسب المئوية
            const percentageElements = document.querySelectorAll('.stats-card span span');
            percentageElements.forEach(element => {
                if (element.textContent.includes('%')) {
                    // إضافة تأثير نبض للنسب المئوية
                    element.style.animation = 'pulse 2s infinite';

                    // إضافة tooltip للنسب المئوية
                    const percentage = parseFloat(element.textContent.replace(/[+%-]/g, ''));
                    if (percentage > 0) {
                        element.title = 'زيادة في النشاط مقارنة بالأسبوع الماضي';
                        element.style.cursor = 'help';
                    } else if (percentage < 0) {
                        element.title = 'انخفاض في النشاط مقارنة بالأسبوع الماضي';
                        element.style.cursor = 'help';
                    } else {
                        element.title = 'نفس مستوى النشاط مقارنة بالأسبوع الماضي';
                        element.style.cursor = 'help';
                    }
                }
            });

            // إضافة CSS للنبض والتفاعل والتصميم المتجاوب
            const dashboardStyle1 = document.createElement('style');
            dashboardStyle1.textContent = `
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.7; }
                }
                .stats-card:hover .text-green-200,
                .stats-card:hover .text-red-200 {
                    animation: none;
                    transform: scale(1.1);
                    transition: transform 0.3s ease;
                }
                .stats-card {
                    position: relative;
                    overflow: hidden;
                }
                .stats-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                    transition: left 0.5s;
                }
                .stats-card:hover::before {
                    left: 100%;
                }
                .text-green-200, .text-red-200 {
                    position: relative;
                    z-index: 2;
                }

                                /* التصميم المتجاوب للشاشات الصغيرة */
                @media (max-width: 768px) {
                    .stats-card {
                        padding: 1rem !important;
                        margin-bottom: 1rem;
                    }

                    .stats-card .text-3xl {
                        font-size: 1.5rem !important;
                    }

                    .stats-card .text-4xl {
                        font-size: 2rem !important;
                    }

                    .stats-card .text-sm {
                        font-size: 0.75rem !important;
                    }

                    .stats-card .text-xs {
                        font-size: 0.625rem !important;
                    }

                    .bg-white {
                        padding: 1rem !important;
                        margin-bottom: 1rem;
                    }

                    .bg-white .text-5xl {
                        font-size: 2.5rem !important;
                    }

                    .bg-white .text-xl {
                        font-size: 1.125rem !important;
                    }

                    .space-y-4 > div {
                        margin-bottom: 0.75rem !important;
                    }

                    .space-y-4 .p-4 {
                        padding: 0.75rem !important;
                    }

                    .space-y-4 .text-sm {
                        font-size: 0.75rem !important;
                    }

                    .space-y-4 .text-xs {
                        font-size: 0.625rem !important;
                    }

                    .fab-btn {
                        width: 3rem !important;
                        height: 3rem !important;
                        font-size: 1rem !important;
                    }

                    .chart-btn {
                        padding: 0.5rem 1rem !important;
                        font-size: 0.875rem !important;
                    }

                    /* تحسين موقع الأزرار العائمة */
                    .fixed.bottom-6.left-6 {
                        bottom: 1rem !important;
                        left: 1rem !important;
                    }

                    .fixed.bottom-6.left-6 .space-y-3 {
                        gap: 0.5rem !important;
                    }
                }

                                /* التصميم المتجاوب للشاشات الصغيرة جداً */
                @media (max-width: 480px) {
                    .stats-card {
                        padding: 0.75rem !important;
                    }

                    .stats-card .text-3xl {
                        font-size: 1.25rem !important;
                    }

                    .stats-card .text-4xl {
                        font-size: 1.5rem !important;
                    }

                    .bg-white {
                        padding: 0.75rem !important;
                    }

                    .bg-white .text-5xl {
                        font-size: 2rem !important;
                    }

                    .space-y-4 .p-4 {
                        padding: 0.5rem !important;
                    }

                    .space-y-4 .p-3 {
                        padding: 0.5rem !important;
                    }

                    .space-y-4 .mr-3 {
                        margin-right: 0.5rem !important;
                    }

                    .fab-btn {
                        width: 2.5rem !important;
                        height: 2.5rem !important;
                        font-size: 0.875rem !important;
                    }

                    .chart-btn {
                        padding: 0.375rem 0.75rem !important;
                        font-size: 0.75rem !important;
                    }

                    .list-disc {
                        padding-right: 0.75rem !important;
                    }

                    .list-disc li {
                        font-size: 0.75rem !important;
                        margin-bottom: 0.25rem !important;
                    }

                    /* تحسين موقع الأزرار العائمة للشاشات الصغيرة جداً */
                    .fixed.bottom-6.left-6 {
                        bottom: 0.5rem !important;
                        left: 0.5rem !important;
                    }

                    .fixed.bottom-6.left-6 .space-y-3 {
                        gap: 0.25rem !important;
                    }

                    .fab-btn .text-xl {
                        font-size: 0.875rem !important;
                    }
                }

                                /* تحسين عرض النسب المئوية على الشاشات الصغيرة */
                @media (max-width: 640px) {
                    .stats-card .text-blue-100,
                    .stats-card .text-green-100,
                    .stats-card .text-red-100 {
                        font-size: 0.75rem !important;
                        line-height: 1.2 !important;
                    }

                    .stats-card .text-xs {
                        font-size: 0.625rem !important;
                        line-height: 1.1 !important;
                    }

                    .stats-card .block {
                        margin-top: 0.25rem !important;
                    }

                    /* تحسين الجداول على الشاشات الصغيرة */
                    .overflow-x-auto {
                        font-size: 0.75rem !important;
                    }

                    .overflow-x-auto th,
                    .overflow-x-auto td {
                        padding: 0.5rem 0.25rem !important;
                        font-size: 0.75rem !important;
                    }

                    .overflow-x-auto .text-sm {
                        font-size: 0.625rem !important;
                    }

                    .overflow-x-auto .text-xs {
                        font-size: 0.5rem !important;
                    }
                }
            `;
            document.head.appendChild(dashboardStyle1);

            // إضافة تأثيرات إضافية للنسب المئوية
            const percentageSpans = document.querySelectorAll('.text-green-200, .text-red-200');
            percentageSpans.forEach(span => {
                span.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.2)';
                    this.style.transition = 'transform 0.3s ease';
                });

                span.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // إضافة CSS للنبض
            const dashboardStyle2 = document.createElement('style');
            dashboardStyle2.textContent = `
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    background-color: rgba(255, 255, 255, 0.6);
                    pointer-events: none;
                }
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(dashboardStyle2);
            // تأثير الظهور للصفوف في الجدول
            const tableRows = document.querySelectorAll('.table-row');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 100 + 1000);
            });
            // إعداد الرسم البياني
            const salesLabelsRaw = @json(array_keys($summary['sales_last_month_chart'] ?? []));
            const salesLabels = salesLabelsRaw.map(date => {
                const d = new Date(date);
                return (d.getMonth()+1).toString().padStart(2, '0') + '-' + d.getDate().toString().padStart(2, '0');
            });
            const salesData = @json(array_values($summary['sales_last_month_chart'] ?? []));
            const profitData = @json(array_values($summary['profit_last_month_chart'] ?? []));

            // التحقق من وجود عنصر canvas
            const canvas = document.getElementById('salesChart');
            if (!canvas) {
                console.error('Canvas element not found');
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Could not get 2D context');
                return;
            }

            // التحقق من البيانات
            if (!salesLabels.length || !salesData.length) {
                console.warn('No chart data available');
                // إضافة رسالة للمستخدم
                const chartContainer = canvas.parentElement;
                if (chartContainer) {
                    chartContainer.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500"><p>لا توجد بيانات متاحة للرسم البياني</p></div>';
                }
                return;
            }

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'المبيعات اليومية',
                        data: salesData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        borderWidth: 3,
                        pointStyle: 'circle',
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' ' + '{{ $currency }}';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return value;
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: 'rgb(59, 130, 246)',
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 3
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // إخفاء مؤشر التحميل بعد تحميل الرسم البياني
            setTimeout(() => {
                const loadingElement = document.getElementById('chartLoading');
                if (loadingElement) {
                    loadingElement.style.opacity = '0';
                    loadingElement.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        loadingElement.style.display = 'none';
                    }, 500);
                }
            }, 1000);

            // تأثير التبديل بين الرسوم البيانية
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // التحقق من وجود الرسم البياني
                    if (!chart) {
                        console.error('Chart not initialized');
                        return;
                    }

                    document.querySelectorAll('.chart-btn').forEach(b => {
                        b.classList.remove('active', 'bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    });
                    this.classList.add('active', 'bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-600');

                    if (this.dataset.chart === 'sales') {
                        chart.data.datasets[0].label = 'المبيعات اليومية';
                        chart.data.datasets[0].data = salesData;
                        chart.data.datasets[0].borderColor = 'rgb(59, 130, 246)';
                        chart.data.datasets[0].backgroundColor = 'rgba(59, 130, 246, 0.1)';
                    } else if (this.dataset.chart === 'profit') {
                        chart.data.datasets[0].label = 'الأرباح اليومية';
                        chart.data.datasets[0].data = profitData;
                        chart.data.datasets[0].borderColor = 'rgb(139, 92, 246)';
                        chart.data.datasets[0].backgroundColor = 'rgba(139, 92, 246, 0.1)';
                    }
                    chart.update();
                });
            });
            // تأثير الأزرار العائمة
            const fabBtns = document.querySelectorAll('.fab-btn');
            fabBtns.forEach((btn, index) => {
                btn.style.transform = 'translateY(100px) scale(0)';
                setTimeout(() => {
                    btn.style.transition = 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                    btn.style.transform = 'translateY(0) scale(1)';
                }, index * 100 + 1500);
            });
            // تأثير التمرير للكشف عن العناصر
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            // مراقبة جميع العناصر القابلة للكشف
            document.querySelectorAll('.bg-white').forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.6s ease';
                observer.observe(element);
            });
            // تأثير النقر المتموج
            function createRipple(event) {
                const button = event.currentTarget;
                const circle = document.createElement('span');
                const diameter = Math.max(button.clientWidth, button.clientHeight);
                const radius = diameter / 2;
                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
                circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
                circle.classList.add('ripple');
                const ripple = button.getElementsByClassName('ripple')[0];
                if (ripple) {
                    ripple.remove();
                }
                button.appendChild(circle);
            }
            // إضافة تأثير النقر المتموج للأزرار
            document.querySelectorAll('.fab-btn, .chart-btn').forEach(btn => {
                btn.addEventListener('click', createRipple);
            });
            // إضافة CSS للتأثير المتموج
            const dashboardStyle3 = document.createElement('style');
            dashboardStyle3.textContent = `
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    background-color: rgba(255, 255, 255, 0.6);
                    pointer-events: none;
                }
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(dashboardStyle3);
        });
    </script>
    @endpush
@endsection

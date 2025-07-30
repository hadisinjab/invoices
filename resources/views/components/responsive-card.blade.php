@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null, 'trendValue' => null])

@php
    $colorClasses = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
        'orange' => 'from-orange-500 to-orange-600',
        'teal' => 'from-teal-500 to-teal-600',
    ];

    $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="stats-card bg-gradient-to-br {{ $colorClass }} text-white rounded-2xl p-4 sm:p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex-1">
            <div class="text-2xl sm:text-3xl font-bold mb-2 counter" data-target="{{ $value }}">0</div>
            <div class="text-sm sm:text-base font-medium opacity-90">{{ $title }}</div>

            @if($trend && $trendValue)
                <div class="mt-3 flex items-center text-sm opacity-80">
                    <i class="fas fa-{{ $trend === 'up' ? 'trending-up' : 'trending-down' }} mr-2"></i>
                    <span>
                        @if($trendValue >= 0)
                            <span class="font-semibold text-green-200">+{{ $trendValue }}%</span>
                        @else
                            <span class="font-semibold text-red-200">{{ $trendValue }}%</span>
                        @endif
                        <span class="opacity-80"> من الفترة السابقة</span>
                    </span>
                </div>
            @endif
        </div>

        <div class="text-3xl sm:text-4xl opacity-80 flex-shrink-0">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</div>

<style>
@media (max-width: 640px) {
    .stats-card {
        padding: 1rem;
    }

    .stats-card .counter {
        font-size: 1.5rem;
    }

    .stats-card .text-sm {
        font-size: 0.875rem;
    }
}
</style>

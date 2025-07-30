@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')
    <style>
        @media print {
            body * { visibility: hidden !important; }
            #print-area, #print-area * { visibility: visible !important; }
            #print-area { position: absolute; left: 0; top: 0; width: 100vw; background: white; z-index: 9999; }
        }
    </style>
    <div id="print-area">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">فاتورة رقم #{{ $invoice->invoice_number }}</h1>

            {{-- معلومات العميل --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-semibold">العميل</h2>
                    <p class="text-sm text-gray-700">{{ $invoice->client->name ?? '-' }}</p>
                    <p class="text-sm text-gray-700">{{ $invoice->client->address ?? '-' }}</p>
                </div>
                <div>
                    <h2 class="text-lg font-semibold">معلومات الفاتورة</h2>
                    <p class="text-sm text-gray-700">التاريخ: {{ $invoice->date }}</p>
                    <p class="text-sm text-gray-700">الحالة: <span class="font-bold {{ $invoice->status == 'مقبوضة' ? 'text-green-600' : 'text-red-600' }}">{{ $invoice->status }}</span></p>
                    <p class="text-sm text-gray-700">النوع: {{ $invoice->type }}</p>
                    <p class="text-sm text-gray-700">العملة: {{ $userCurrency }}</p>
                    @if($invoice->received_currency && $invoice->received_currency !== $userCurrency)
                        <p class="text-sm text-gray-700">العملة المستلمة: {{ $invoice->received_currency }}</p>
                        <p class="text-sm text-gray-700">سعر الصرف: 1 {{ $invoice->received_currency }} = {{ $invoice->exchange_rate }} {{ $userCurrency }}</p>
                    @endif
                </div>
            </div>

            {{-- جدول المنتجات / الخدمات --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right border border-gray-300">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 border">المنتج</th>
                            <th class="p-3 border">الكمية</th>
                            <th class="p-3 border">السعر ({{ $userCurrency }})</th>
                            @if($invoice->received_currency && $invoice->exchange_rate && $invoice->received_currency !== $userCurrency)
                                <th class="p-3 border">السعر ({{ $invoice->received_currency }})</th>
                            @endif
                            <th class="p-3 border">الإجمالي ({{ $userCurrency }})</th>
                            @if($invoice->received_currency && $invoice->exchange_rate && $invoice->received_currency !== $userCurrency)
                                <th class="p-3 border">الإجمالي ({{ $invoice->received_currency }})</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr class="border-t">
                                <td class="p-3 border">{{ $item->name }}</td>
                                <td class="p-3 border">{{ $item->quantity }}</td>
                                <td class="p-3 border">{{ number_format($item->price, 2) }} {{ $userCurrency }}</td>
                                @if($invoice->received_currency && $invoice->exchange_rate && $invoice->received_currency !== $userCurrency)
                                    <td class="p-3 border">
                                        {{ number_format($item->price_received ?? $item->price / $invoice->exchange_rate, 2) }} {{ $invoice->received_currency }}
                                    </td>
                                @endif
                                <td class="p-3 border">{{ number_format($item->quantity * $item->price, 2) }} {{ $userCurrency }}</td>
                                @if($invoice->received_currency && $invoice->exchange_rate && $invoice->received_currency !== $userCurrency)
                                    <td class="p-3 border">
                                        {{ number_format($item->total_received ?? ($item->quantity * $item->price) / $invoice->exchange_rate, 2) }} {{ $invoice->received_currency }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ملخص الفاتورة --}}
            <div class="text-sm text-gray-700 mt-6">
                <div class="flex justify-between">
                    <span>الإجمالي:</span>
                    <span>{{ number_format($total, 2) }} {{ $userCurrency }}</span>
                </div>
                <div class="flex justify-between">
                    <span>الحسم:</span>
                    <span>{{ number_format($invoice->discount ?? 0, 2) }} {{ $userCurrency }}</span>
                </div>
                <div class="flex justify-between font-bold text-green-700">
                    <span>الإجمالي بعد الحسم:</span>
                    <span>{{ number_format($total - ($invoice->discount ?? 0), 2) }} {{ $userCurrency }}</span>
                </div>
            </div>
        </div>
    </div>
    {{-- زر طباعة --}}
    <div class="mt-6 text-left">
        <a href="#" onclick="window.print();return false;" class="border border-blue-600 text-blue-600 px-6 py-2 rounded hover:bg-blue-50 font-bold">طباعة الفاتورة</a>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'إضافة مصروف جديد')

@push('styles')
    <link href="{{ asset('css/expenses-index.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="min-h-screen p-6 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="glass-card max-w-lg mx-auto fade-in">
        <div class="flex items-center gap-4 mb-6">
            <div class="icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">إضافة مصروف جديد</h1>
                <p class="text-gray-600 mt-1">أدخل بيانات المصروف الجديد أدناه</p>
            </div>
        </div>
        <form id="expenseForm" autocomplete="off" class="space-y-5">
            <div>
                <label class="block mb-1 font-semibold text-gray-700">اسم المستلزم <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-input w-full" placeholder="مثال: قرطاسية" required>
                <span class="text-xs text-red-500 hidden" id="nameError"></span>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">المبلغ <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" class="form-input w-full" placeholder="مثال: 1500" min="0" step="0.01" required>
                <span class="text-xs text-red-500 hidden" id="amountError"></span>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">التاريخ <span class="text-red-500">*</span></label>
                <input type="date" name="date" id="date" class="form-input w-full" required>
                <span class="text-xs text-red-500 hidden" id="dateError"></span>
            </div>
            <div>
                <label class="block mb-1 font-semibold text-gray-700">التعليق</label>
                <textarea name="comment" id="comment" class="form-input w-full" rows="2" placeholder="ملاحظات إضافية (اختياري)"></textarea>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button type="button" class="btn-secondary" onclick="window.location.href='{{ route('expenses.index') }}'">
                    <i class="fas fa-arrow-right ml-2"></i> إلغاء
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save ml-2"></i> حفظ
                </button>
            </div>
            <div id="formAlert" class="mt-4 hidden"></div>
        </form>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/create-expense.js') }}"></script>
@endpush
@endsection

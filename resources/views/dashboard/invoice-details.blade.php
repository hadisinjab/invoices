@extends('layouts.app')

@section('title', 'تفاصيل المنتجات')

@section('content')
    <div class="bg-white shadow rounded-lg p-6 max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">تفاصيل المنتجات</h1>
        <form method="POST" action="#">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right border border-gray-300" id="products-table">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 border">اسم المنتج</th>
                            <th class="p-3 border">الكمية</th>
                            <th class="p-3 border">السعر</th>
                            <th class="p-3 border">الإجمالي</th>
                            <th class="p-3 border">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border"><input type="text" name="products[0][name]" class="border rounded px-2 py-1 w-full max-w-xs" required></td>
                            <td class="p-2 border"><input type="number" name="products[0][quantity]" class="border rounded px-2 py-1 w-full max-w-xs quantity" min="1" value="1" required></td>
                            <td class="p-2 border"><input type="number" name="products[0][price]" class="border rounded px-2 py-1 w-full max-w-xs price" min="0" step="0.01" value="0" required></td>
                            <td class="p-2 border"><input type="text" class="border rounded px-2 py-1 w-full max-w-xs total" value="0" readonly></td>
                            <td class="p-2 border"><button type="button" class="remove-row text-red-600 font-bold">✖</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- ملخص الفاتورة -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200 max-w-md mx-auto">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">مجموع الإجماليات:</span>
                    <span id="subtotal" class="font-bold text-blue-700">0</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold">الحسم:</span>
                    <input type="number" id="discount" name="discount" class="border rounded px-2 py-1 w-32 text-right" min="0" step="0.01" value="0">
                </div>
                <div class="flex justify-between items-center mt-4 border-t pt-2">
                    <span class="font-semibold text-lg">الإجمالي بعد الحسم:</span>
                    <span id="total-after-discount" class="font-bold text-green-700 text-lg">0</span>
                </div>
            </div>
            <button type="button" id="add-row" class="mt-4 border border-blue-600 text-blue-600 px-4 py-1 rounded hover:bg-blue-50">إضافة منتج</button>
            <button type="submit" class="mt-4 ml-2 border border-gray-700 text-black px-6 py-2 rounded hover:bg-gray-200 font-bold">حفظ التفاصيل</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let rowIdx = 1;
            const table = document.getElementById('products-table').getElementsByTagName('tbody')[0];
            const subtotalSpan = document.getElementById('subtotal');
            const discountInput = document.getElementById('discount');
            const totalAfterDiscountSpan = document.getElementById('total-after-discount');

            function updateTotals() {
                let subtotal = 0;
                table.querySelectorAll('.total').forEach(input => {
                    subtotal += parseFloat(input.value) || 0;
                });
                subtotalSpan.textContent = subtotal.toFixed(2);
                let discount = parseFloat(discountInput.value) || 0;
                let totalAfterDiscount = subtotal - discount;
                if (totalAfterDiscount < 0) totalAfterDiscount = 0;
                totalAfterDiscountSpan.textContent = totalAfterDiscount.toFixed(2);
            }

            document.getElementById('add-row').onclick = function () {
                const row = table.insertRow();
                row.innerHTML = `
                    <td class="p-2 border"><input type="text" name="products[${rowIdx}][name]" class="border rounded px-2 py-1 w-full max-w-xs" required></td>
                    <td class="p-2 border"><input type="number" name="products[${rowIdx}][quantity]" class="border rounded px-2 py-1 w-full max-w-xs quantity" min="1" value="1" required></td>
                    <td class="p-2 border"><input type="number" name="products[${rowIdx}][price]" class="border rounded px-2 py-1 w-full max-w-xs price" min="0" step="0.01" value="0" required></td>
                    <td class="p-2 border"><input type="text" class="border rounded px-2 py-1 w-full max-w-xs total" value="0" readonly></td>
                    <td class="p-2 border"><button type="button" class="remove-row text-red-600 font-bold">✖</button></td>
                `;
                rowIdx++;
                updateTotals();
            };
            table.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-row')) {
                    if (table.rows.length > 1) e.target.closest('tr').remove();
                    updateTotals();
                }
            });
            table.addEventListener('input', function(e) {
                if (e.target && (e.target.classList.contains('quantity') || e.target.classList.contains('price'))) {
                    const row = e.target.closest('tr');
                    const qty = row.querySelector('.quantity').valueAsNumber || 0;
                    const price = row.querySelector('.price').valueAsNumber || 0;
                    row.querySelector('.total').value = (qty * price).toFixed(2);
                    updateTotals();
                }
            });
            discountInput.addEventListener('input', updateTotals);
            // تحديث القيم عند التحميل الأولي
            updateTotals();
        });
    </script>
@endsection

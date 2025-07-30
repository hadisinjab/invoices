// دالة حساب الإجمالي بعد الحسم - متاحة عالمياً
function updateTotalAfterDiscount() {
    let total = 0;
    document.querySelectorAll('#products-table .total-base').forEach(function(span) {
        total += parseFloat(span.textContent) || 0;
    });
    let discount = parseFloat(document.getElementById('discount').value) || 0;
    let after = total - discount;
    if (after < 0) after = 0;
    document.getElementById('total-after-discount').textContent = after.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function () {
    window.rowIdx = 1; // جعل rowIdx متاحًا عالمياً
    window.updateTotalAfterDiscount = updateTotalAfterDiscount; // جعل الدالة متاحة عالمياً
    const table = document.getElementById('products-table').getElementsByTagName('tbody')[0];

    // منطق تغيير نوع الفاتورة
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        // حذف كل الصفوف
        while (table.rows.length > 0) {
            table.deleteRow(0);
        }
        // إعادة تعيين rowIdx
        window.rowIdx = 1;
        // إضافة صف جديد حسب النوع الجديد
        addNewProductRow(table, window.rowIdx);
        window.rowIdx++;
    });

    // تحميل المنتجات الحالية عند تحميل الصفحة
    if (window.oldInvoiceItems && table.rows.length === 0) {
        if (window.oldInvoiceItems.length > 0) {
            window.oldInvoiceItems.forEach(item => {
                addNewProductRow(table, window.rowIdx, item);
                window.rowIdx++;
            });
        } else {
            // Add an empty row if there are no items
            addNewProductRow(table, window.rowIdx);
            window.rowIdx++;
        }
    }

    // ربط زر إضافة منتج
    const addRowBtn = document.getElementById('add-row');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function() {
            addNewProductRow(table, window.rowIdx);
            window.rowIdx++;
        });
    }
});

function addNewProductRow(table, rowIdx, item = null) {
    const invoiceType = document.querySelector('select[name="type"]').value;
    const row = table.insertRow();
    let productCellHtml = '';

    // منطق الشراء: input مع select2 tags
    if (invoiceType === 'شراء') {
        productCellHtml = `
            <input type="text" name="products[${rowIdx}][name]" class="table-input product-name" placeholder="اسم المنتج" autocomplete="off" required value="${item && item.name ? item.name : ''}">
        `;
    } else { // 'بيع', 'مردودات بيع', 'مردودات شراء'
        productCellHtml = `
            <select name="products[${rowIdx}][product_id]" class="table-input product-select" required></select>
        `;
    }

    row.innerHTML = `
        <td>
            ${productCellHtml}
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][quantity]" class="table-input quantity" min="1" value="${item && item.quantity !== undefined ? item.quantity : 1}" required>
        </td>
        <td style="position:relative;">
            <input type="number" name="products[${rowIdx}][price]" class="table-input price-base" min="0" step="0.01" value="${item && item.price !== undefined ? item.price : ''}" required placeholder="سعر بالعملة الأساسية">
            <button type="button" class="show-prices-btn" style="background:none;border:none;cursor:pointer;position:absolute;left:2px;top:6px;z-index:2;" title="عرض آخر الأسعار"><i class="fas fa-history"></i></button>
            <div class="last-prices-popover" style="display:none;position:absolute;left:0;top:36px;z-index:10;background:#fff;border:1px solid #ccc;padding:6px 8px;border-radius:6px;box-shadow:0 2px 8px #0001;"></div>
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][price_received]" class="table-input price-received" min="0" step="0.01" value="${item && item.price_received !== undefined ? item.price_received : ''}" readonly placeholder="سعر بالعملة المستلمة">
        </td>
        <td>
            <span class="total-base">0</span>
        </td>
        <td>
            <span class="total-received">0</span>
        </td>
        <td>
            <button type="button" class="remove-row btn-remove"><i class="fas fa-times"></i></button>
        </td>
    `;

    // Initialize Select2
    const $productSelect = $(row).find('.product-select');
    if ($productSelect.length > 0) {
        $productSelect.select2({
            placeholder: 'اختر المنتج...',
            dir: 'rtl',
            width: '100%',
            ajax: {
                url: window.routes?.productsAutocomplete || '/api/products/autocomplete',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.products.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name,
                                ...product // Pass all product data
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        $productSelect.on('select2:select', function(e) {
            const data = e.params.data;
            const priceBaseInput = row.querySelector('.price-base');
            const qtyInput = row.querySelector('.quantity');

            if (invoiceType === 'بيع' || invoiceType === 'مردودات شراء') {
                if(data.quantity) qtyInput.max = data.quantity;
            } else {
                qtyInput.removeAttribute('max');
            }

            if (invoiceType === 'بيع' || invoiceType === 'مردودات بيع') {
                priceBaseInput.value = data.sale_price || '';
            } else if (invoiceType === 'مردودات شراء') {
                priceBaseInput.value = data.purchase_price || '';
            }

            updateProductRow(row);
        });
    }

    const $productName = $(row).find('.product-name');
    if ($productName.length > 0) {
        $productName.select2({
            tags: true,
            placeholder: 'اختر أو أدخل اسم المنتج...',
            dir: 'rtl',
            width: '100%',
            ajax: {
                url: window.routes?.productsAutocomplete || '/api/products/autocomplete',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term || '' }; },
                processResults: function (data) {
                    return { results: data.products ? data.products.map(p => ({ id: p.name, text: p.name })) : [] };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function (data) { return data.text; },
            templateSelection: function (data) { return data.text || data.id; }
        });

        $productName.on('select2:select', function(e) {
            const selectedData = e.params.data;
            const name = selectedData.text || selectedData.id;
            // Fix: Manually update the visual selection text
            $(this).next('.select2-container').find('.select2-selection__rendered').text(name);

            const found = window.productsList.find(p => p.name.trim() === name.trim());
            if (found) {
                row.querySelector('.price-base').value = found.purchase_price || '';
            } else {
                row.querySelector('.price-base').value = '';
            }
            updateProductRow(row);
        });
    }


    // Fill data if item exists (for initial load)
    if (item) {
        row.querySelector('.quantity').value = item.quantity || 1;
        row.querySelector('.price-base').value = item.price || '';
        if(row.querySelector('.price-received')) row.querySelector('.price-received').value = item.price_received || '';

        if (invoiceType === 'شراء') {
            const $productNameInput = $(row).find('.product-name');
            // Set the value and trigger change for Select2
            $productNameInput.val(item.name || '').trigger('change');
            // Fix: Manually set the text for the initial load
            $productNameInput.next('.select2-container').find('.select2-selection__rendered').text(item.name || '');
        } else {
            const $productSelect = $(row).find('.product-select');
            const option = new Option(item.name || '', item.product_id, true, true);
            $productSelect.append(option).trigger('change');
        }
    }


    // Event Listeners
    row.querySelector('.quantity').addEventListener('input', () => updateProductRow(row));
    row.querySelector('.price-base').addEventListener('input', () => updateProductRow(row));
    document.getElementById('exchange_rate')?.addEventListener('input', () => updateAllRows());
    document.getElementById('received_currency')?.addEventListener('change', () => updateAllRows());
    row.querySelector('.remove-row').addEventListener('click', function() {
        row.remove();
        updateTotalAfterDiscount();
    });

    updateProductRow(row);
}

function updateAllRows() {
    document.querySelectorAll('#products-table tbody tr').forEach(row => {
        updateProductRow(row);
    });
}

function updateProductRow(row) {
    const priceBaseInput = row.querySelector('.price-base');
    const priceReceivedInput = row.querySelector('.price-received');
    const quantityInput = row.querySelector('.quantity');
    const totalBaseSpan = row.querySelector('.total-base');
    const totalReceivedSpan = row.querySelector('.total-received');

    const exchangeRate = parseFloat(document.getElementById('exchange_rate')?.value) || 0;
    const baseCurrency = window.baseCurrency;
    const receivedCurrency = document.getElementById('received_currency')?.value || '';

    if (receivedCurrency && receivedCurrency !== baseCurrency && exchangeRate > 0) {
        priceReceivedInput.value = (parseFloat(priceBaseInput.value || 0) / exchangeRate).toFixed(2);
    } else {
        priceReceivedInput.value = '';
    }

    const priceBase = parseFloat(priceBaseInput.value) || 0;
    const priceReceived = parseFloat(priceReceivedInput.value) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;

    totalBaseSpan.textContent = (priceBase * quantity).toFixed(2);
    totalReceivedSpan.textContent = (priceReceived * quantity).toFixed(2);

    updateTotalAfterDiscount();
}


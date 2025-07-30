document.addEventListener('DOMContentLoaded', function () {
    window.rowIdx = 1; // جعل rowIdx متاحًا عالمياً
    const table = document.getElementById('products-table').getElementsByTagName('tbody')[0];

    // تعبئة رقم الفاتورة تلقائيًا إذا كان متوفرًا من backend
    const invoiceNumberInput = document.querySelector('input[name="invoice_number"]');
    if (invoiceNumberInput && window.lastInvoiceNumber) {
        invoiceNumberInput.value = parseInt(window.lastInvoiceNumber) + 1;
    }
    // تفعيل Select2 للعميل
    initializeClientSelect();
    // ربط زر إضافة منتج
    const addRowBtn = document.getElementById('add-row');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function() {
            addNewProductRow(table, window.rowIdx);
            window.rowIdx++;
        });
    }
    // إضافة أول صف تلقائياً عند تحميل الصفحة إذا لم يوجد صفوف
    if (table.rows.length === 0) {
        addNewProductRow(table, window.rowIdx);
        window.rowIdx++;
    }
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

    // منطق إظهار/إخفاء حقل سعر الصرف بناءً على العملة المستلمة
    var baseCurrency = (document.querySelector('meta[name="user-currency"]')?.getAttribute('content') || window.baseCurrency || 'SYP').trim().toUpperCase();
    var receivedCurrency = document.getElementById('received_currency');
    var exchangeRateGroup = document.getElementById('exchange_rate_group');
    function toggleExchangeRateField() {
        if (receivedCurrency && exchangeRateGroup) {
            if (receivedCurrency.value.trim().toUpperCase() !== baseCurrency) {
                exchangeRateGroup.style.display = '';
            } else {
                exchangeRateGroup.style.display = 'none';
            }
        }
    }
    if (receivedCurrency) {
        receivedCurrency.addEventListener('change', toggleExchangeRateField);
        toggleExchangeRateField(); // استدعاء عند التحميل
    }
});

function initializeClientSelect() {
    const clientSelect = $('#client_id');
    if (clientSelect.length) {
        clientSelect.select2({
            placeholder: 'ابحث عن عميل...',
            dir: 'rtl',
            ajax: {
                url: window.routes?.clientsAutocomplete || '/clients/autocomplete',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            language: {
                noResults: function() {
                    return 'لا توجد نتائج';
                },
                searching: function() {
                    return 'جاري البحث...';
                },
                inputTooShort: function() {
                    return 'يرجى إدخال حرف واحد على الأقل';
                }
            }
        });
    }
}

function addNewProductRow(table, rowIdx) {
    const invoiceType = document.querySelector('select[name="type"]').value;
    const row = table.insertRow();
    let productCellHtml = '';

    // منطق الشراء: input مع select2 tags
    if (invoiceType === 'شراء') {
        productCellHtml = `
            <input type="text" name="products[${rowIdx}][name]" class="table-input product-name" placeholder="اسم المنتج" autocomplete="off" required>
        `;
    } else if (invoiceType === 'بيع') {
        // منطق البيع: select مع select2 (فقط المنتجات المتوفرة)
        productCellHtml = `
            <select name="products[${rowIdx}][product_id]" class="table-input product-select" required></select>
        `;
    } else if (invoiceType === 'مردودات بيع') {
        // منطق مردودات البيع: select مع select2 (جميع المنتجات، بدون tags)
        productCellHtml = `
            <select name="products[${rowIdx}][product_id]" class="table-input product-select" required></select>
        `;
    } else if (invoiceType === 'مردودات شراء') {
        // منطق مردودات الشراء: select مع select2 (جميع المنتجات، بدون tags)
        productCellHtml = `
            <select name="products[${rowIdx}][product_id]" class="table-input product-select" required></select>
        `;
    }

    row.innerHTML = `
        <td>
            ${productCellHtml}
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][quantity]" class="table-input quantity" min="1" value="1" required>
        </td>
        <td style="position:relative;">
            <input type="number" name="products[${rowIdx}][price]" class="table-input price-base" min="0" step="0.01" value="" required placeholder="سعر بالعملة الأساسية">
            ${invoiceType === 'شراء' ? '<button type="button" class="show-prices-btn" style="background:none;border:none;cursor:pointer;position:absolute;left:2px;top:6px;z-index:2;" title="عرض آخر الأسعار"><i class="fas fa-history"></i></button>' : ''}
            <div class="last-prices-popover" style="display:none;position:absolute;left:0;top:36px;z-index:10;background:#fff;border:1px solid #ccc;padding:6px 8px;border-radius:6px;box-shadow:0 2px 8px #0001;"></div>
        </td>
        <td>
            <input type="number" name="products[${rowIdx}][price_received]" class="table-input price-received" min="0" step="0.01" value="" readonly placeholder="سعر بالعملة المستلمة">
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

    // منطق الشراء
    if (invoiceType === 'شراء') {
        const $productName = $(row).find('.product-name');
        $productName.select2({
            tags: true,
            placeholder: 'اختر أو أدخل اسم المنتج...',
            dir: 'rtl',
            width: '100%',
            ajax: {
                url: window.routes?.productsAutocomplete || '/api/products/autocomplete',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term || '' }; // أرسل الكلمة حتى لو فارغة
                },
                processResults: function (data) {
                    let results = [];
                    if (data.products) {
                        results = data.products.map(function(product) {
                            return {
                                id: product.name,
                                text: product.name,
                                value: product.name,
                                existing: true
                            };
                        });
                    }
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 0, // صفر حتى تظهر القائمة عند الضغط
            language: {
                noResults: function() {
                    return 'منتج جديد';
                },
                searching: function() {
                    return 'جاري البحث...';
                },
                inputTooShort: function() {
                    return 'يرجى إدخال حرف واحد على الأقل';
                }
            },
            templateResult: function (data) {
                if (data.existing) {
                    return data.text;
                } else {
                    return $('<span>').text(data.text).append('<span style="color: #999; margin-right: 5px;"> (جديد)</span>');
                }
            },
            templateSelection: function (data) { return data.text || data.id; },
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                };
            }
        });
        // عند التركيز على الحقل، افتح القائمة فورًا
        $productName.on('focus', function() {
            $(this).select2('open');
        });

        // عند اختيار أو إدخال اسم منتج
        $productName.on('select2:select', function(e) {
            const selectedData = e.params.data;
            const name = selectedData.text || selectedData.id;

            // اجبر القيمة في input
            $productName.val(name);
            $productName[0].setAttribute('value', name);
            $productName[0].setAttribute('placeholder', '');

            // اجبر select2 أن يعرض القيمة بدل placeholder
            setTimeout(() => {
                $(this).next('.select2-container').find('.select2-selection__rendered').text(name);
            }, 0);

            // تحديث الأسعار والمنطق
            updateProductPrices(row, name);
        });

        // عند تغيير القيمة يدوياً
        $productName.on('change', function() {
            const name = $(this).val();
            if (name) {
                updateProductPrices(row, name);
            }
        });

        // دالة تحديث أسعار المنتج
        function updateProductPrices(row, name) {
            const lastPricesPopover = row.querySelector('.last-prices-popover');
            const showPricesBtn = row.querySelector('.show-prices-btn');

            if (name && invoiceType === 'شراء') {
                fetch(`/invoice/public/api/products/last-purchase-prices-by-name?name=${encodeURIComponent(name)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.prices && data.prices.length > 0) {
                            lastPricesPopover.innerHTML = data.prices.map(p =>
                                `<button type="button" class="btn-last-price" style="margin:2px;padding:2px 8px;font-size:12px;">${p}</button>`
                            ).join(' ');
                            if (showPricesBtn) {
                                showPricesBtn.style.display = 'inline-block';
                                showPricesBtn.onclick = function(e) {
                                    e.stopPropagation();
                                    lastPricesPopover.style.display = lastPricesPopover.style.display === 'block' ? 'none' : 'block';
                                };
                                document.addEventListener('click', function hidePopover(ev) {
                                    if (!lastPricesPopover.contains(ev.target) && ev.target !== showPricesBtn) {
                                        lastPricesPopover.style.display = 'none';
                                        document.removeEventListener('click', hidePopover);
                                    }
                                });
                                lastPricesPopover.querySelectorAll('.btn-last-price').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        row.querySelector('.price-base').value = this.textContent;
                                        updateProductRow(row);
                                        lastPricesPopover.style.display = 'none';
                                    });
                                });
                            }
                        } else {
                            lastPricesPopover.innerHTML = '<span style="color:#aaa">منتج جديد - لا توجد أسعار سابقة</span>';
                            if (showPricesBtn) {
                                showPricesBtn.style.display = 'inline-block';
                                showPricesBtn.onclick = function(e) {
                                    e.stopPropagation();
                                    lastPricesPopover.style.display = lastPricesPopover.style.display === 'block' ? 'none' : 'block';
                                };
                                document.addEventListener('click', function hidePopover(ev) {
                                    if (!lastPricesPopover.contains(ev.target) && ev.target !== showPricesBtn) {
                                        lastPricesPopover.style.display = 'none';
                                        document.removeEventListener('click', hidePopover);
                                    }
                                });
                            }
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching last purchase prices:', error);
                        lastPricesPopover.innerHTML = '<span style="color:#aaa">منتج جديد - لا توجد أسعار سابقة</span>';
                        if (showPricesBtn) {
                            showPricesBtn.style.display = 'inline-block';
                            showPricesBtn.onclick = function(e) {
                                e.stopPropagation();
                                lastPricesPopover.style.display = lastPricesPopover.style.display === 'block' ? 'none' : 'block';
                            };
                            document.addEventListener('click', function hidePopover(ev) {
                                if (!lastPricesPopover.contains(ev.target) && ev.target !== showPricesBtn) {
                                    lastPricesPopover.style.display = 'none';
                                    document.removeEventListener('click', hidePopover);
                                }
                            });
                        }
                    });
            } else {
                lastPricesPopover.innerHTML = '';
                if (showPricesBtn) showPricesBtn.style.display = 'none';
            }

            // تحديث السعر من قائمة المنتجات الموجودة
            if (window.productsList && Array.isArray(window.productsList)) {
                const found = window.productsList.find(p => p.name.trim() === name.trim());
                if (found) {
                    row.querySelector('.price-base').value = found.purchase_price || '';
                } else {
                    row.querySelector('.price-base').value = '';
                }
            }
            updateProductRow(row);
        }
    }
    // منطق البيع
    else if (invoiceType === 'بيع') {
        $(row).find('.product-select').select2({
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
                    let results = [];
                    if (data.products) {
                        results = data.products
                            .filter(function(product) {
                                if (invoiceType === 'بيع') {
                                    return product.quantity > 0;
                                }
                                return true;
                            })
                            .map(function(product) {
                                return {
                                    id: product.id,
                                    text: product.name,
                                    available: product.quantity,
                                    sale_price: product.sale_price
                                };
                            });
                    }
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 1
        });
        $(row).find('.product-select').on('select2:select', function(e) {
            const data = e.params.data;
            const qtyInput = row.querySelector('.quantity');
            const priceBaseInput = row.querySelector('.price-base');
            if (qtyInput) {
                qtyInput.max = data.available;
                if (parseInt(qtyInput.value) > data.available) qtyInput.value = data.available;
            }
            if (priceBaseInput) priceBaseInput.value = data.sale_price;
            updateProductRow(row);
        });
    }
    // منطق مردودات البيع
    else if (invoiceType === 'مردودات بيع') {
        $(row).find('.product-select').select2({
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
                    let results = [];
                    if (data.products) {
                        results = data.products.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name,
                                sale_price: product.sale_price
                            };
                        });
                    }
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 1
        });
        // عند اختيار المنتج: السعر يعبأ تلقائياً من سعر المبيع وقابل للتعديل
        $(row).find('.product-select').on('select2:select', function(e) {
            const data = e.params.data;
            const priceBaseInput = row.querySelector('.price-base');
            if (priceBaseInput) priceBaseInput.value = data.sale_price;
            // الكمية: لا max
            const qtyInput = row.querySelector('.quantity');
            if (qtyInput) qtyInput.removeAttribute('max');
            updateProductRow(row);
        });
    }
    // منطق مردودات الشراء
    else if (invoiceType === 'مردودات شراء') {
        $(row).find('.product-select').select2({
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
                    let results = [];
                    if (data.products) {
                        results = data.products.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name,
                                available: product.quantity,
                                purchase_price: product.purchase_price
                            };
                        });
                    }
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 1
        });
        // عند اختيار المنتج: السعر يعبأ تلقائياً من سعر الشراء، الكمية max = المتوفر
        $(row).find('.product-select').on('select2:select', function(e) {
            const data = e.params.data;
            const priceBaseInput = row.querySelector('.price-base');
            if (priceBaseInput) priceBaseInput.value = data.purchase_price;
            const qtyInput = row.querySelector('.quantity');
            if (qtyInput) {
                qtyInput.max = data.available;
                if (parseInt(qtyInput.value) > data.available) qtyInput.value = data.available;
            }
            updateProductRow(row);
        });
    }

    // أحداث عامة
    row.querySelector('.quantity').addEventListener('input', function() {
        if (parseInt(this.value) < 1) this.value = 1;
        updateProductRow(row);
    });
    row.querySelector('.price-base').addEventListener('input', function() {
        updateProductRow(row);
    });
    document.getElementById('exchange_rate')?.addEventListener('input', function() {
        updateProductRow(row);
    });
    document.getElementById('received_currency')?.addEventListener('change', function() {
        updateProductRow(row);
    });
    row.querySelector('.remove-row').addEventListener('click', function() {
        row.remove();
    });
    updateProductRow(row);
}

function updateProductRow(row) {
    const priceBaseInput = row.querySelector('.price-base');
    const priceReceivedInput = row.querySelector('.price-received');
    const quantityInput = row.querySelector('.quantity');
    const totalBaseSpan = row.querySelector('.total-base');
    const totalReceivedSpan = row.querySelector('.total-received');
    const exchangeRate = parseFloat(document.getElementById('exchange_rate')?.value || 0);
    // تحديث العملة الأساسية من الصفحة مباشرة
    const baseCurrency = (document.querySelector('meta[name="user-currency"]')?.getAttribute('content') || window.baseCurrency || 'SAR').trim().toUpperCase();
    const receivedCurrency = document.getElementById('received_currency')?.value || '';
    const userCurrency = baseCurrency;

    // منطق السعر بالعملة المستلمة
    if (receivedCurrency && receivedCurrency !== userCurrency && exchangeRate) {
        priceReceivedInput.value = (parseFloat(priceBaseInput.value) / exchangeRate).toFixed(2);
        priceReceivedInput.readOnly = true;
    } else {
        priceReceivedInput.value = '';
        priceReceivedInput.readOnly = true;
    }

    // الإجماليات
    const priceBase = parseFloat(priceBaseInput.value) || 0;
    const priceReceived = parseFloat(priceReceivedInput.value) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;
    totalBaseSpan.textContent = (priceBase * quantity).toFixed(2);
    if (receivedCurrency && receivedCurrency !== userCurrency && exchangeRate) {
        totalReceivedSpan.textContent = (priceReceived * quantity).toFixed(2);
    } else {
        totalReceivedSpan.textContent = '0';
    }

    // تحديث الإجمالي بعد الحسم
    if (typeof window.updateTotalAfterDiscount === 'function') {
        window.updateTotalAfterDiscount();
    }
}

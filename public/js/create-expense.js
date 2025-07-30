// معالجة التحقق من صحة البيانات وحفظ المصروف في localStorage

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('expenseForm');
    const nameInput = document.getElementById('name');
    const amountInput = document.getElementById('amount');
    const dateInput = document.getElementById('date');
    const commentInput = document.getElementById('comment');
    const alertBox = document.getElementById('formAlert');

    // عناصر الأخطاء
    const nameError = document.getElementById('nameError');
    const amountError = document.getElementById('amountError');
    const dateError = document.getElementById('dateError');

    function showError(input, errorElem, message) {
        input.classList.add('border-red-500');
        errorElem.textContent = message;
        errorElem.classList.remove('hidden');
    }
    function clearError(input, errorElem) {
        input.classList.remove('border-red-500');
        errorElem.textContent = '';
        errorElem.classList.add('hidden');
    }
    function showAlert(type, message) {
        alertBox.className = 'mt-4 px-4 py-3 rounded text-sm';
        if (type === 'success') {
            alertBox.classList.add('bg-green-100', 'text-green-800');
        } else {
            alertBox.classList.add('bg-red-100', 'text-red-800');
        }
        alertBox.textContent = message;
        alertBox.classList.remove('hidden');
    }
    function clearAlert() {
        alertBox.className = 'mt-4 hidden';
        alertBox.textContent = '';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearAlert();
        let valid = true;

        // التحقق من اسم المستلزم
        if (!nameInput.value.trim()) {
            showError(nameInput, nameError, 'يرجى إدخال اسم المستلزم');
            valid = false;
        } else {
            clearError(nameInput, nameError);
        }
        // التحقق من المبلغ
        if (!amountInput.value.trim() || isNaN(amountInput.value) || Number(amountInput.value) <= 0) {
            showError(amountInput, amountError, 'يرجى إدخال مبلغ صحيح أكبر من صفر');
            valid = false;
        } else {
            clearError(amountInput, amountError);
        }
        // التحقق من التاريخ
        if (!dateInput.value) {
            showError(dateInput, dateError, 'يرجى اختيار التاريخ');
            valid = false;
        } else {
            clearError(dateInput, dateError);
        }

        if (!valid) return;

        // تجهيز بيانات المصروف
        const expense = {
            name: nameInput.value.trim(),
            amount: parseFloat(amountInput.value),
            expense_date: dateInput.value, // <-- انتبه للاسم هنا
            comment: commentInput.value.trim()
        };

        fetch('/expenses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(expense)
        })
        .then(res => res.json())
        .then(data => {
            if (data.errors) {
                showAlert('error', 'حدث خطأ في البيانات المدخلة');
            } else {
                showAlert('success', 'تم حفظ المصروف بنجاح!');
                form.reset();
                setTimeout(() => {
                    window.location.href = '/expenses';
                }, 1000);
            }
        })
        .catch(() => {
            showAlert('error', 'حدث خطأ أثناء حفظ المصروف');
        });
    });
});

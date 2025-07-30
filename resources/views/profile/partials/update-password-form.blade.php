{{-- resources/views/profile/partials/update-password-form.blade.php --}}

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700" dir="rtl">
    <div class="p-6 sm:p-8">
        <section>
            <header class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-green-100 dark:bg-green-900 p-2 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            تحديث كلمة المرور
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمناً.
                        </p>
                    </div>
                </div>
            </header>

            <!-- نموذج تحديث كلمة المرور -->
            <form method="post" action="{{ route('password.update') }}" class="space-y-6" x-data="passwordForm()">
                @csrf
                @method('put')

                <!-- كلمة المرور الحالية -->
                <div class="space-y-2">
                    <x-input-label for="update_password_current_password" value="كلمة المرور الحالية" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button
                                type="button"
                                @click="togglePassword('current')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <svg x-show="!showCurrent" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showCurrent" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-text-input
                            id="update_password_current_password"
                            name="current_password"
                            x-bind:type="showCurrent ? 'text' : 'password'"
                            class="block w-full pr-10 pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200 text-right"
                            autocomplete="current-password"
                            placeholder="أدخل كلمة المرور الحالية"
                        />
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

                <!-- كلمة المرور الجديدة -->
                <div class="space-y-2">
                    <x-input-label for="update_password_password" value="كلمة المرور الجديدة" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button
                                type="button"
                                @click="togglePassword('new')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <svg x-show="!showNew" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showNew" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-text-input
                            id="update_password_password"
                            name="password"
                            x-bind:type="showNew ? 'text' : 'password'"
                            class="block w-full pr-10 pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200 text-right"
                            autocomplete="new-password"
                            placeholder="أدخل كلمة المرور الجديدة"
                            @input="checkPasswordStrength($event.target.value)"
                        />
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />

                    <!-- مؤشر قوة كلمة المرور -->
                    <div x-show="passwordStrength.show" class="mt-2">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="passwordStrength.color"
                                    :style="`width: ${passwordStrength.width}%`"
                                ></div>
                            </div>
                            <span class="text-xs font-medium" :class="passwordStrength.textColor" x-text="passwordStrength.text"></span>
                        </div>
                        <ul class="mt-2 text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <li class="flex items-center" :class="passwordStrength.criteria.length ? 'text-green-600 dark:text-green-400' : ''">
                                <svg class="w-3 h-3 ml-1" :class="passwordStrength.criteria.length ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                8 أحرف على الأقل
                            </li>
                            <li class="flex items-center" :class="passwordStrength.criteria.uppercase ? 'text-green-600 dark:text-green-400' : ''">
                                <svg class="w-3 h-3 ml-1" :class="passwordStrength.criteria.uppercase ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                حرف كبير واحد على الأقل
                            </li>
                            <li class="flex items-center" :class="passwordStrength.criteria.number ? 'text-green-600 dark:text-green-400' : ''">
                                <svg class="w-3 h-3 ml-1" :class="passwordStrength.criteria.number ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                رقم واحد على الأقل
                            </li>
                            <li class="flex items-center" :class="passwordStrength.criteria.special ? 'text-green-600 dark:text-green-400' : ''">
                                <svg class="w-3 h-3 ml-1" :class="passwordStrength.criteria.special ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                رمز خاص واحد على الأقل (!@#$%^&*)
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- تأكيد كلمة المرور -->
                <div class="space-y-2">
                    <x-input-label for="update_password_password_confirmation" value="تأكيد كلمة المرور" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button
                                type="button"
                                @click="togglePassword('confirm')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <svg x-show="!showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showConfirm" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-text-input
                            id="update_password_password_confirmation"
                            name="password_confirmation"
                            x-bind:type="showConfirm ? 'text' : 'password'"
                            class="block w-full pr-10 pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200 text-right"
                            autocomplete="new-password"
                            placeholder="أعد إدخال كلمة المرور الجديدة"
                        />
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                </div>

                <!-- قسم الإرسال -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <x-primary-button class="px-6 py-2.5 bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500 rounded-lg font-medium transition duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            حفظ كلمة المرور
                        </x-primary-button>

                        @if (session('status') === 'password-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-x-4"
                                x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform translate-x-4"
                                x-init="setTimeout(() => show = false, 3000)"
                                class="text-sm text-green-600 dark:text-green-400 flex items-center font-medium"
                            >
                                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                تم تحديث كلمة المرور بنجاح!
                            </p>
                        @endif
                    </div>

                    <!-- نصائح الأمان -->
                    <div class="hidden lg:block">
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            استخدم كلمة مرور قوية
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<style>
    /* تحسينات الرسوم المتحركة والتأثيرات */
    .transform {
        transition: transform 0.2s ease-in-out;
    }

    .hover\:scale-105:hover {
        transform: scale(1.05);
    }

    /* حالات التركيز */
    input:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }

    /* دعم محسن للغة العربية */
    input[type="password"] {
        text-align: right;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* تحسينات الوضع المظلم */
    .dark input:focus {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }

    /* تحسين المسافات للعربية */
    .space-x-reverse > :not([hidden]) ~ :not([hidden]) {
        --tw-space-x-reverse: 1;
        margin-right: calc(0.75rem * var(--tw-space-x-reverse));
        margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
    }

    /* تحسين النصوص العربية */
    h2, p, label, li {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }

    /* مؤشر قوة كلمة المرور */
    .password-strength-indicator {
        transition: all 0.3s ease;
    }
</style>

<script>
    function passwordForm() {
        return {
            showCurrent: false,
            showNew: false,
            showConfirm: false,
            passwordStrength: {
                show: false,
                width: 0,
                color: 'bg-red-500',
                textColor: 'text-red-600 dark:text-red-400',
                text: '',
                criteria: {
                    length: false,
                    uppercase: false,
                    number: false,
                    special: false
                }
            },

            togglePassword(field) {
                if (field === 'current') this.showCurrent = !this.showCurrent;
                if (field === 'new') this.showNew = !this.showNew;
                if (field === 'confirm') this.showConfirm = !this.showConfirm;
            },

            checkPasswordStrength(password) {
                if (!password) {
                    this.passwordStrength.show = false;
                    return;
                }

                this.passwordStrength.show = true;

                // فحص المعايير
                this.passwordStrength.criteria.length = password.length >= 8;
                this.passwordStrength.criteria.uppercase = /[A-Z]/.test(password);
                this.passwordStrength.criteria.number = /[0-9]/.test(password);
                this.passwordStrength.criteria.special = /[!@#$%^&*(),.?":{}|<>]/.test(password);

                // حساب القوة
                const score = Object.values(this.passwordStrength.criteria).filter(Boolean).length;

                switch(score) {
                    case 0:
                    case 1:
                        this.passwordStrength.width = 25;
                        this.passwordStrength.color = 'bg-red-500';
                        this.passwordStrength.textColor = 'text-red-600 dark:text-red-400';
                        this.passwordStrength.text = 'ضعيف';
                        break;
                    case 2:
                        this.passwordStrength.width = 50;
                        this.passwordStrength.color = 'bg-yellow-500';
                        this.passwordStrength.textColor = 'text-yellow-600 dark:text-yellow-400';
                        this.passwordStrength.text = 'متوسط';
                        break;
                    case 3:
                        this.passwordStrength.width = 75;
                        this.passwordStrength.color = 'bg-blue-500';
                        this.passwordStrength.textColor = 'text-blue-600 dark:text-blue-400';
                        this.passwordStrength.text = 'جيد';
                        break;
                    case 4:
                        this.passwordStrength.width = 100;
                        this.passwordStrength.color = 'bg-green-500';
                        this.passwordStrength.textColor = 'text-green-600 dark:text-green-400';
                        this.passwordStrength.text = 'قوي';
                        break;
                }
            },

            init() {
                // إخفاء تلقائي لرسائل النجاح
                this.$watch('$el', () => {
                    const successMessages = this.$el.querySelectorAll('[x-data*="show: true"]');
                    successMessages.forEach(msg => {
                        setTimeout(() => {
                            if (msg.__x) msg.__x.$data.show = false;
                        }, 3000);
                    });
                });
            }
        }
    }

    // تحسين تجربة المستخدم
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة تأثيرات تفاعلية للحقول
        const inputs = document.querySelectorAll('input[type="password"]');

        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.relative').classList.add('ring-2', 'ring-green-200', 'dark:ring-green-800');
            });

            input.addEventListener('blur', function() {
                this.closest('.relative').classList.remove('ring-2', 'ring-green-200', 'dark:ring-green-800');
            });
        });

        // تحسين أمان النسخ واللصق
        const passwordInputs = document.querySelectorAll('input[name*="password"]');
        passwordInputs.forEach(input => {
            input.addEventListener('paste', function(e) {
                // السماح بلصق كلمة المرور
                setTimeout(() => {
                    if (this.name === 'password') {
                        // تحديث مؤشر القوة عند اللصق
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    }
                }, 100);
            });
        });
    });
</script>

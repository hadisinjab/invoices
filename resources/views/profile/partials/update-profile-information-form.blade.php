{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-200 dark:border-gray-700" dir="rtl">
    <div class="p-6 sm:p-8">
        <section>
            <header class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            معلومات الملف الشخصي
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            قم بتحديث معلومات ملفك الشخصي وعنوان البريد الإلكتروني الخاص بحسابك.
                        </p>
                    </div>
                </div>
            </header>

            <!-- نموذج التحقق المخفي -->
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <!-- النموذج الرئيسي للملف الشخصي -->
            <form method="post" action="{{ route('profile.update') }}" class="space-y-6" x-data="profileForm()">
                @csrf
                @method('patch')

                <!-- حقل الاسم -->
                <div class="space-y-2">
                    <x-input-label for="name" value="الاسم" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="block w-full pr-10 pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200 text-right"
                            :value="old('name', $user->name)"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="أدخل اسمك الكامل"
                        />
                    </div>
                    <x-input-error class="mt-2 text-sm text-red-600 dark:text-red-400" :messages="$errors->get('name')" />
                </div>

                <!-- حقل البريد الإلكتروني -->
                <div class="space-y-2">
                    <x-input-label for="email" value="البريد الإلكتروني" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            class="block w-full pr-10 pl-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200 text-right"
                            :value="old('email', $user->email)"
                            required
                            autocomplete="username"
                            placeholder="example@domain.com"
                        />
                    </div>
                    <x-input-error class="mt-2 text-sm text-red-600 dark:text-red-400" :messages="$errors->get('email')" />

                    <!-- تنبيه التحقق من البريد الإلكتروني -->
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border-r-4 border-amber-400 rounded-l-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="mr-3">
                                    <p class="text-sm text-amber-700 dark:text-amber-200">
                                        عنوان بريدك الإلكتروني غير مُتحقق منه.
                                        <button
                                            form="send-verification"
                                            class="underline text-sm text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-800 mr-2 font-medium transition duration-150"
                                        >
                                            انقر هنا لإعادة إرسال رسالة التحقق.
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 transform scale-90"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            x-init="setTimeout(() => show = false, 5000)"
                                            class="mt-2 font-medium text-sm text-green-600 dark:text-green-400 flex items-center"
                                        >
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- قسم الإرسال -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <x-primary-button class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-blue-500 rounded-lg font-medium transition duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            حفظ التغييرات
                        </x-primary-button>

                        @if (session('status') === 'profile-updated')
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
                                تم الحفظ بنجاح!
                            </p>
                        @endif
                    </div>

                    <!-- معلومات آخر تحديث -->
                    <div class="hidden sm:block">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            آخر تحديث: {{ $user->updated_at->diffForHumans() }}
                        </p>
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
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* دعم محسن للغة العربية */
    input[type="text"],
    input[type="email"] {
        text-align: right;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* تحسينات الوضع المظلم */
    .dark input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    /* تحسين المسافات للعربية */
    .space-x-reverse > :not([hidden]) ~ :not([hidden]) {
        --tw-space-x-reverse: 1;
        margin-right: calc(0.75rem * var(--tw-space-x-reverse));
        margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
    }

    /* تحسين تخطيط الأزرار */
    button {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* تحسين النصوص العربية */
    h2, p, label {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
</style>

<script>
    function profileForm() {
        return {
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
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"]');

        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.relative').classList.add('ring-2', 'ring-blue-200', 'dark:ring-blue-800');
            });

            input.addEventListener('blur', function() {
                this.closest('.relative').classList.remove('ring-2', 'ring-blue-200', 'dark:ring-blue-800');
            });
        });
    });
</script>

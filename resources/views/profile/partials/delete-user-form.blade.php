<section class="space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8" dir="rtl">
    <header class="text-center">
        <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
            حذف الحساب
        </h2>

        <div class="max-w-2xl mx-auto">
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                بمجرد حذف حسابك، سيتم حذف جميع البيانات والموارد المرتبطة به نهائياً. قبل حذف حسابك، يُرجى تحميل أي بيانات أو معلومات تود الاحتفاظ بها.
            </p>
        </div>
    </header>

    <div class="flex justify-center">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-8 py-3 text-base font-medium bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:ring-4 focus:ring-red-200 dark:focus:ring-red-800 transform transition-all duration-200 hover:scale-105 shadow-lg"
        >
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            حذف الحساب نهائياً
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md mx-auto" dir="rtl">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <!-- Header Modal -->
                <div class="p-8 text-center border-b border-gray-200 dark:border-gray-700">
                    <div class="mx-auto w-20 h-20 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        هل أنت متأكد من حذف حسابك؟
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        بمجرد حذف حسابك، سيتم حذف جميع البيانات والموارد المرتبطة به نهائياً. يُرجى إدخال كلمة المرور للتأكيد.
                    </p>
                </div>

                <!-- Body Modal -->
                <div class="p-8 space-y-6">
                    <div class="space-y-2">
                        <x-input-label for="password" value="كلمة المرور" class="text-sm font-medium text-gray-700 dark:text-gray-300" />

                        <div class="relative">
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                                placeholder="أدخل كلمة المرور"
                                required
                            />
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>

                        <x-input-error :messages="$errors->userDeletion->get('password')" class="text-sm text-red-600 dark:text-red-400" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <x-secondary-button
                            x-on:click="$dispatch('close')"
                            class="flex-1 px-6 py-3 text-sm font-medium bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200 rounded-lg"
                        >
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            إلغاء
                        </x-secondary-button>

                        <x-danger-button class="flex-1 px-6 py-3 text-sm font-medium bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 transition-all duration-200 rounded-lg">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            حذف نهائي
                        </x-danger-button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</section>

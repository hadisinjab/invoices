@props(['items'])

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="container-responsive">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 hidden sm:block">نظام الفواتير</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                @foreach($items as $item)
                    <a href="{{ $item['url'] }}"
                       class="text-gray-600 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request()->url() === $item['url'] ? 'text-primary-600 bg-primary-50' : '' }}">
                        <i class="{{ $item['icon'] }} ml-2"></i>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors duration-200">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                </button>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 space-x-reverse text-gray-700 hover:text-primary-600 transition-colors duration-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user ml-2"></i>
                            الملف الشخصي
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog ml-2"></i>
                            الإعدادات
                        </a>
                        <hr class="my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt ml-2"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="md:hidden p-2 text-gray-600 hover:text-primary-600 transition-colors duration-200">
                    <i class="fas fa-bars text-lg" x-show="!open"></i>
                    <i class="fas fa-times text-lg" x-show="open"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @foreach($items as $item)
                <a href="{{ $item['url'] }}"
                   class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-colors duration-200 {{ request()->url() === $item['url'] ? 'text-primary-600 bg-primary-50' : '' }}">
                    <i class="{{ $item['icon'] }} ml-3"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'قائمة المنتجات')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body style="background: #f7f7fa; min-height: 100vh;">
    <div>
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>

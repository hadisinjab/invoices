<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CashBoxController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/home', function () {
    return view('welcome');
})->name('home');

// Email Verification Routes - Removed duplicate routes that are already in auth.php

// Auth Routes - Added directly to ensure they work
Route::middleware('guest')->group(function () {
    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/code', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset.code');

    Route::post('reset-password/code', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', \App\Http\Controllers\Auth\EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', \App\Http\Controllers\Auth\VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::put('password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $controller = new InvoiceController();
        $summary = $controller->getDashboardSummary();
        $currency = Auth::user()->currency ?? 'ريال سعودي';
        $clientsCount = \App\Models\Client::where('user_id', Auth::id())->count();
        $invoicesCount = \App\Models\Invoice::where('user_id', Auth::id())->count();
        return view('dashboard', compact('summary', 'currency', 'clientsCount', 'invoicesCount'));
    })->middleware(['verified', 'block.admin.from.user'])->name('dashboard');

    Route::get('/dashboard/invoice', function () {
        $clientsCount = \App\Models\Client::where('user_id', Auth::id())->count();
        $invoicesCount = \App\Models\Invoice::where('user_id', Auth::id())->count();
        return view('dashboard.invoice', compact('clientsCount', 'invoicesCount'));
    })->middleware('block.admin.from.user');

    Route::get('/dashboard/invoice/create', [InvoiceController::class, 'create'])->name('invoices.create')->middleware('block.admin.from.user');
    Route::post('/dashboard/invoice/store', [InvoiceController::class, 'store'])->name('invoices.store')->middleware('block.admin.from.user');
    Route::get('/dashboard/invoice/{id}', [InvoiceController::class, 'show'])->name('invoices.show')->middleware('block.admin.from.user');
    Route::get('/dashboard/invoice/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit')->middleware('block.admin.from.user');
    Route::put('/dashboard/invoice/{id}', [InvoiceController::class, 'update'])->name('invoices.update')->middleware('block.admin.from.user');
    Route::get('/dashboard/invoice/details', function () {
        $clientsCount = \App\Models\Client::where('user_id', Auth::id())->count();
        $invoicesCount = \App\Models\Invoice::where('user_id', Auth::id())->count();
        return view('dashboard.invoice-details', compact('clientsCount', 'invoicesCount'));
    })->middleware('block.admin.from.user');
    Route::get('/dashboard/invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware('block.admin.from.user');
    Route::get('/dashboard/invoices/api', [InvoiceController::class, 'apiIndex'])->name('invoices.api')->middleware('block.admin.from.user');
    Route::delete('/dashboard/invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy')->middleware('block.admin.from.user');
    Route::get('/invoices/{id}/print', [InvoiceController::class, 'print'])->name('invoices.print')->middleware('block.admin.from.user');
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download')->middleware('block.admin.from.user');
    Route::get('/invoices/{id}/data', [InvoiceController::class, 'getInvoiceData'])->name('invoices.data')->middleware('block.admin.from.user');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('block.admin.from.user');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('block.admin.from.user');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('block.admin.from.user');
    Route::patch('/profile/currency', [\App\Http\Controllers\ProfileController::class, 'updateCurrency'])->name('profile.currency.update')->middleware('block.admin.from.user');

    // عملاء
    Route::get('/dashboard/clients', [\App\Http\Controllers\ClientController::class, 'index'])->name('clients.index')->middleware('block.admin.from.user');
    Route::get('/dashboard/clients/create', [\App\Http\Controllers\ClientController::class, 'create'])->name('clients.create')->middleware('block.admin.from.user');
    Route::post('/dashboard/clients', [\App\Http\Controllers\ClientController::class, 'store'])->name('clients.store')->middleware('block.admin.from.user');
    Route::get('/api/clients-autocomplete', [\App\Http\Controllers\ClientController::class, 'autocomplete'])->name('clients.autocomplete')->middleware('block.admin.from.user');
    Route::get('/api/clients/{id}/invoices', [\App\Http\Controllers\ClientController::class, 'getInvoices'])->name('clients.invoices')->middleware('block.admin.from.user');
    Route::get('/dashboard/clients/{id}/edit', [\App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit')->middleware('block.admin.from.user');
    Route::put('/dashboard/clients/{id}', [\App\Http\Controllers\ClientController::class, 'update'])->name('clients.update')->middleware('block.admin.from.user');
    Route::delete('/dashboard/clients/{id}', [\App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy')->middleware('block.admin.from.user');

    // Products
    Route::get('/dashboard/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index')->middleware('block.admin.from.user');
    Route::post('/products/{id}/update-sale-price', [\App\Http\Controllers\ProductController::class, 'updateSalePrice'])->name('products.updateSalePrice')->middleware('block.admin.from.user');
    Route::post('/products/{id}/update-purchase-price', [\App\Http\Controllers\ProductController::class, 'updatePurchasePrice'])->name('products.updatePurchasePrice')->middleware('block.admin.from.user');
    Route::get('/api/products/autocomplete', [\App\Http\Controllers\ProductController::class, 'autocomplete'])->name('products.autocomplete')->middleware('block.admin.from.user');
    Route::get('/api/products/search', [\App\Http\Controllers\ProductController::class, 'search'])->name('products.search')->middleware('block.admin.from.user');
    Route::get('/api/products/{id}/last-purchase-prices', [\App\Http\Controllers\InvoiceController::class, 'lastPurchasePrices'])->name('products.lastPurchasePrices')->middleware('block.admin.from.user');
    Route::get('/api/products/last-purchase-prices-by-name', [\App\Http\Controllers\InvoiceController::class, 'lastPurchasePricesByName'])->name('products.lastPurchasePricesByName')->middleware('block.admin.from.user');

    // Expenses
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->names([
        'index' => 'expenses.index',
        'create' => 'expenses.create',
        'store' => 'expenses.store',
        'show' => 'expenses.show',
        'edit' => 'expenses.edit',
        'update' => 'expenses.update',
        'destroy' => 'expenses.destroy',
    ])->middleware('block.admin.from.user');

    Route::get('/expenses/create', function () {
        $clientsCount = \App\Models\Client::where('user_id', Auth::id())->count();
        $invoicesCount = \App\Models\Invoice::where('user_id', Auth::id())->count();
        return view('dashboard.create-expense', compact('clientsCount', 'invoicesCount'));
    })->name('expenses.create')->middleware('block.admin.from.user');

    Route::get('/api/expenses', [\App\Http\Controllers\ExpenseController::class, 'apiIndex'])->name('expenses.api')->middleware('block.admin.from.user');

    // Cash Box
    Route::get('/dashboard/cash-box', [CashBoxController::class, 'index'])->middleware(['verified', 'block.admin.from.user'])->name('cash-box.index');
    Route::get('/dashboard/cash-box/data', [CashBoxController::class, 'getData'])->middleware(['verified', 'block.admin.from.user'])->name('cash-box.data');
    Route::post('/dashboard/cash-box/initial-balance', [CashBoxController::class, 'updateInitialBalance'])->middleware(['verified', 'block.admin.from.user'])->name('cash-box.initial-balance');

    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'daily'])->middleware(['auth', 'verified', 'block.admin.from.user'])->name('reports.daily');

    // Admin Routes
    Route::middleware(['auth', 'verified', 'admin.only'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
    });
});

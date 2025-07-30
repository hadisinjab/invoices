<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Models\Invoice;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // مشاركة عدد العملاء وعدد الفواتير مع جميع الصفحات حسب المستخدم الحالي
        View::composer('*', function ($view) {
            $user = Auth::user();
            if ($user) {
                $clientsCount = \App\Models\Client::where('user_id', $user->id)->count();
                $invoicesCount = \App\Models\Invoice::where('user_id', $user->id)->count();
            } else {
                $clientsCount = 0;
                $invoicesCount = 0;
            }
            $view->with('clientsCount', $clientsCount)->with('invoicesCount', $invoicesCount);
        });
    }
}

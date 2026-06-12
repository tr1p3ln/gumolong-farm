<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $notifCount = 0;
            if (Auth::check()) {
                $notifCount = DB::table('notifikasi')
                    ->where('user_id', Auth::id())
                    ->where('sudah_dibaca', false)
                    ->count();
            }
            $view->with('notifCount', $notifCount);
        });
    }
}
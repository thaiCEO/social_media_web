<?php

namespace App\Providers;

use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share('friendRequestsCount', function () {
        $userId = Auth::id();
        if (!$userId) {
            return 0;
        }
        return Friendship::where('receiver_id', $userId)
            ->where('status', 'pending')
            ->count();
    });

           
    }
}

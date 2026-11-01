<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapThree();

        Gate::define('manage-master-data', fn (User $user): bool => $user->role === UserRole::Koordinator);
        Gate::define('view-recap', fn (User $user): bool => $user->hasRole(UserRole::Relawan, UserRole::Koordinator));
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Admin: akses penuh mengelola master layanan (CRUD) & operasional laundry
        Gate::define('manage-laundry', fn (User $user): bool => (bool) $user->is_admin);

        // Admin & Kasir: akses dashboard pesanan masuk, update status, cetak struk, & laporan
        Gate::define('operate-laundry', fn (User $user): bool => (bool) $user->is_admin || $user->role === 'kasir');

        if (env('VERCEL') || env('VERCEL_ENV')) {
            URL::forceScheme('https');
        }
    }
}

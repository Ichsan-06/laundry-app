<?php

namespace App\Providers;

use App\Models\Machine;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\MachinePolicy;
use App\Policies\MemberPolicy;
use App\Policies\OutletPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Paginator::useTailwind();

        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Member::class, MemberPolicy::class);
        Gate::policy(Machine::class, MachinePolicy::class);
        Gate::policy(Outlet::class, OutletPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}

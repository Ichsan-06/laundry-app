<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function __construct(
        private readonly SubscriptionAccessService $subscriptionAccessService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $subscription = $this->subscriptionAccessService->currentSubscription($user->tenant);

        if (! $this->subscriptionAccessService->isExpired($subscription)) {
            return $next($request);
        }

        $allowedRoutes = [
            'dashboard',
            'logout',
            'billing.index',
            'billing.renew',
            'home',
        ];

        if ($request->route() && in_array($request->route()->getName(), $allowedRoutes, true)) {
            return $next($request);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', 'Langganan Anda telah habis.');
    }
}

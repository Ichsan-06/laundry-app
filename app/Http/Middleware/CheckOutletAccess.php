<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOutletAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isSuperAdmin() || $user->isOwner()) {
            return $next($request);
        }

        $allowedOutletId = $user->outlet_id;

        if (! $allowedOutletId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $requestOutletId = $request->input('outlet_id');

        if ($requestOutletId && $requestOutletId !== $allowedOutletId) {
            abort(Response::HTTP_FORBIDDEN);
        }

        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if (is_object($parameter) && isset($parameter->outlet_id) && $parameter->outlet_id !== $allowedOutletId) {
                abort(Response::HTTP_FORBIDDEN);
            }

            if (is_object($parameter) && method_exists($parameter, 'getKeyName') && $parameter->getTable() === 'outlets' && $parameter->getKey() !== $allowedOutletId) {
                abort(Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}

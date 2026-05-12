<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type, string ...$abilities): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($type === 'role' && ! $user->hasAnyRole($abilities)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($type === 'permission' && ! $user->hasAnyPermission($abilities)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

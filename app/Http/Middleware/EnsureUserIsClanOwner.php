<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClanOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $user->load('ownedClan');
        $clan = $request->route('clan');
        $ownedClan = $user?->ownedClan;

        if (! $user || ! $user->hasRole('clan_owner') || ! $clan || $ownedClan?->id !== $clan->id) {
            abort(403);
        }

        View::share('clan', $ownedClan);

        return $next($request);
    }
}

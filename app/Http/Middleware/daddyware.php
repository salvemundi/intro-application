<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class daddyware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        session(['intendedUrl' => $request->url()]);

        $userId = session('id');

        $groupsObj = session('groups');

        if (!$userId || !$groupsObj) {
            return redirect("/login");
        }

        $groups = array_map(fn($val) => $val->getId(), $groupsObj);

        $allowedGroups = [
            '7b7cf6e2-b440-4eff-8cdc-ac753bfb27d7', // alle leden hihi
        ];

        if (!array_intersect($allowedGroups, $groups)) {
            return abort(401);
        }

        return $next($request);
    }
}

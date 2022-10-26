<?php

namespace App\Http\Middleware;

use App\Permission\DashboardPermission;
use Closure;

class ModeratorAuthenticate {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if(!auth('sanctum')->guest() && auth('sanctum')->user()->checkPermission(new DashboardPermission())) return $next($request);
        return response('', 403);
    }

}

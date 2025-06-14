<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BigCommerceApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        config(['bigcommerce.api_url' => 'https://api.bigcommerce.com/stores/0zqs1nv0tn/v3/']);
        config(['bigcommerce.client_id' => 'apwex06tn6jcalu6e5nyi66v12qyxh8']);
        config(['bigcommerce.access_token' => '39sxf3m68y72nl1w2b1oj0ugt7fu6kh']);

        return $next($request);
    }
}

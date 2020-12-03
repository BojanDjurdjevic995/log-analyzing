<?php

namespace App\Http\Middleware;

use Closure;

class VerifyApiToken
{
    private $api_token = '4hxMF7YAVeMuiDn18XcJIhcKVZPvMzqTu7F360xr';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->headers->get('api_token') == $this->api_token)
            return $next($request);
        return response()->json(['success' => false, 'error_msg' => 'Token does not exist or does not match'], 403);
    }
}

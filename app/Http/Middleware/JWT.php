<?php

namespace App\Http\Middleware;

use Closure;

class JWT
{
    public function handle($request, Closure $next, $guard = null)
    {
        \Config::set('app.currentenv', 'api');
        $token = $request->header('Authorization');
        $result = \JWTAuth::verify($token);
        if (isset($result['data']->payload->os)) {
            \Config::set('app.current_platform', $result['data']->payload->os);
        }
        throw_if(!$result['result'] || is_null($result['user']), new \App\Exceptions\VaildateException('header', __('common.jwt_invalid'), 100004));
        \Auth::guard('web')->loginUsingId($result['user']->id);
        return $next($request);
    }
}

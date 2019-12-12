<?php

namespace App\Http\Middleware;

use App\Exceptions\Code;
use App\Models\Maintain as MaintainModel;
use Carbon\Carbon;
use Closure;

class Maintain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = \Cache::get(MaintainModel::CACHE_KEY);

        $now = Carbon::now();

        $startTime = Carbon::parse($data['start_datetime']);

        $endTime = Carbon::parse($data['end_datetime']);

        if ($data['switch'] == MaintainModel::MAINTAIN_SWITCH_ON && 
            ($now >= $startTime && $now <= $endTime) &&
            $data['platform'] == MaintainModel::PLATFORM_ALL
            ) {
            throw new \App\Exceptions\VaildateException('header', 'maintain', Code::MAINTAIN);
        }
        return $next($request);
    }
}

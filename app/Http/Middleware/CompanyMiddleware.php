<?php

namespace App\Http\Middleware;

use Closure;
use App\Company;
use App\UserTimestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CompanyMiddleware
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
    // $userId = optional(Auth::user())->id;
    // if ($userId && request()->header('company-id')) {
    //   UserTimestamp::create([
    //     'company_id' =>  request()->header('company-id'),
    //     'user_id' =>  $userId,
    //     "timestamp" =>  Carbon::now(),
    //   ]);
    // }

    $request['company'] = Company::where('id', '=', request()->header('company-id'))->first();

    return $next($request);
  }
}

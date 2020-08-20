<?php

namespace App\Http\Middleware;

use Closure;
use App\Company;

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
    $request['company'] = Company::where('id' , '=', request()->header('company-id'))->first();

    return $next($request);
  }
}

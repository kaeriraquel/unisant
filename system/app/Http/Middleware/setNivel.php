<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Auth;

class setNivel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::guest())
          return redirect("/login");
        if($request->segment(1) != strtolower(\Auth::user()->nivel->name) && $request->isMethod("get")){
          $str = str_replace($request->segment(1),strtolower(Auth::user()->nivel->name)."/".$request->segment(1),$request->fullURL());
          return redirect($str);
        } else {
          $request->request->add(["nivel"=>\Auth::user()->nivel->name,'nivelpath'=>strtolower(\Auth::user()->nivel->name)]);
        }

        return $next($request);
    }
}

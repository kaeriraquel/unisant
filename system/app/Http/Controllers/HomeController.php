<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard');
    }

    public function log(Request $r)
    {
        \Session::put('admin', md5(\Auth::user()->id));
        \Auth::login(\App\User::whereRAW("md5(id)='".$r->cid."'")->first());
        \Session::put("status","Acceso exitoso");
        return redirect("/home");
    }
    public function ladmin(Request $r)
    {
        if(\Session::has("admin") && \Session::get("admin") == $r->cid){
          \Session::put("status","Regresaste a ser tú");
          \Auth::login(\App\User::whereRAW("md5(id)='".$r->cid."'")->first());
        }
        return redirect("/user");
    }
}

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get("/raquel",function(){
    return "Hola Raquel";
});

Route::get('/', function () {
    return redirect('/login');
});

Route::any('/api', function () {
    return view("users.guest.api");
});
Route::any('/addpay', function () {
    return view("users.guest.addpay");
});
Route::any('/muc', function () {
    return view("users.guest.muc");
});
Route::any('/inscritos', function () {
    return view("users.guest.inscritos");
});

Route::get("/invoice/{codigo}",function(Request $r, $codigo){
  return view("users.guest.invoice",["codigo"=>$codigo]);
});
Route::get("/kardex/{codigo}",function(Request $r, $codigo){
  return view("users.guest.kardex",["codigo"=>$codigo]);
});
Route::post("/facturas/solicitar",function(Request $r){
  return App::make('App\Http\Controllers\\facturas')->solicitar($r);
});


Route::get("/descargar/{codigo}",function(Request $r, $codigo){
  return App::make('App\Http\Controllers\\pagos')->descargar($r,$codigo);
});
Route::get("/apoyo/{codigo}/{ext}",function(Request $r, $codigo,$ext){
  return App::make('App\Http\Controllers\\controlescolar')->descargar($r,$codigo,$ext);
});
Route::get("/ver/{codigo}",function(Request $r, $codigo){
  return App::make('App\Http\Controllers\\pagos')->ver($r,$codigo);
});

Auth::routes();

//


App::setLocale("es");


Route::group(['middleware' => 'nivel'],function(){
  Route::get('{somethingelse}/{someCommand}/comm_{someSubCommand}',function($sel,$someCommand,$someSubCommand,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

  Route::post('{someCommand}/{someSubCommand}',function($someCommand,$someSubCommand,Request $r){
    return App::make('App\Http\Controllers\\'.$someCommand)->$someSubCommand($r);
  })->where(['someCommand'=>'[A-z_]+','someSubCommand'=>'[A-z_]+']);

  Route::get('{n}/{s}',function($n,$s){
    return view("users.$n.$s");
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);

  Route::get('{sede}/{n}/{s}',function($sede,$n,$s){
    return view("users.$sede.$n.$s",["menucommand"=>$n,"itemcommand"=>$s]);
  })->where(['{n}'=>'[A-z]','{s}'=>'[A-z]']);

  Route::get('/home', 'HomeController@index')->name('home');
});

//

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');
});


Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});
// use App\\Http\Controllers\UserController;
// use App\\Http\Controllers\BlogController;
//
// Route::get('/tinymce', function(){
//   return view('tinymce');
// });
//
// Route::get('/about',[UserController::class,'about']);
// Route::get('/blog',[UserController::class,'blogPost']);
//
// Route::get('/test', function(){
//   return view('test');
// });

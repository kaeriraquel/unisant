<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('profile.edit');
    }

    public function nuevo(Request $r){
      $users = \App\User::where("email",$r->email)->count();
      if($users > 0){
        $r->session()->put('error', 'El correo electrónico '.$r->email." ya se encuentra registrado");
        return  redirect()->back();
      }

      $u = [
          "name"=>$r->name,
          "email"=>$r->email,
          "password"=>\Hash::make($r->password),
          "nivel_id" => $r->nivel_id
        ];

      //dd($u);
      $nu = \App\User::create($u);
      \App\sede_usuario::create(["usuario_id"=>$nu->id,"sede_id"=>$r->sede_id]);

      $r->session()->put('status', 'Usuario agregado');
      return  redirect("/user");
    }

    public function actualizar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $u = \App\User::whereRAW("md5(id)='$params->cid'")->first();

      $u->name = $r->name;
      $u->nivel_id = $r->nivel_id;
      if (!empty($r->password)) {
        $u->password = \Hash::make($r->password);
      }
      $u->email = $r->email;
      if($u->sede != NULL){
        $u->sede->sede_id = $r->sede_id;
        $u->sede->save();
      } else {
        \App\sede_usuario::create(["usuario_id"=>$u->id,"sede_id"=>$r->sede_id]);
      }
      $u->save();

      return redirect()->back()->with("status","Usuario actualizado");

    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withStatusPassword(__('Password successfully updated.'));
    }
}

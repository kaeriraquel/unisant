<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class revoes extends Controller
{
    public function guardarensede(Request $r){
      \App\revoes_sedes::create(["sede_id"=>$r->sede_id,"revoe_id"=>$r->revoe_id]);
      $r->session()->put('status', 'Rvoe establecido');
      return redirect("/sede/revoes?cid=".md5($r->sede_id));
    }

    public function guardar(Request $r){
      \App\revoes::create(["nombre"=>$r->nombre,"clave"=>$r->clave,"descr"=>$r->descr])->save();
      return redirect()->back()->with("status","RVOE almacenada");
    }

    public function actualizar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $s = \App\revoes::whereRAW("md5(id)='$params->cid'")->first();

      $s->nombre = $r->nombre;
      $s->descr = $r->descr;
      $s->clave = $r->clave;

      $s->save();

      return redirect()->back()->with("status","RVOE actualizada");
    }

    public function eliminar(Request $r){
      $s = \App\revoes_sedes::whereRAW("md5(id)='$r->cid'")->first()->delete();

      return redirect()->back()->with("status","Lista actualizada");
    }
}

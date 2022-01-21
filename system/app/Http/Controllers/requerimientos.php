<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class requerimientos extends Controller
{
    public function guardar(Request $r){
      \App\conceptos::create(["concepto"=>$r->concepto,"usos"=>$r->usos,"activo"=>$r->activo])->save();
      $r->session()->put('status', 'Concepto guardado');
      return redirect("/requerimientos/lista");
    }

    public function actualizar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $s = \App\conceptos::whereRAW("md5(id)='$params->cid'")->first();

      $s->concepto = $r->concepto;
      $s->usos = $r->usos;
      $s->activo = $r->activo;

      $s->save();

      $r->session()->put('status', 'Concepto actualizado');
      return redirect("/requerimientos/lista");
    }
}

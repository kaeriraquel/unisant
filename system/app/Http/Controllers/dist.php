<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class dist extends Controller
{
    public function debug(Request $r){
      if (session()->has("debug")) {
        $r->session()->put('status', 'Debug desactivado');
        session()->remove("debug");
      } else {
        session()->put("debug",1);
        $r->session()->put('status', 'Debug activado');
      }
      return redirect()->back();
    }
    public function addopciones(Request $r){
      \App\conciliacion_opciones::where("conciliacion_id",$r->conciliacion_id)->delete();
      if(isset($r->opciones)){
        foreach ($r->opciones as $op) {
          \App\conciliacion_opciones::create([
            "concepto_id" => $op,
            "conciliacion_id" => $r->conciliacion_id
          ]);
        }
      }
      $r->session()->put('status', 'Opciones configuradas');
      return redirect()->back();
    }
    public function addgrupo(Request $r){
      $dists = \App\dist_grupos::where("grupo",$r->grupo)->count();
      if($dists > 0){
        $r->session()->put('error', 'Este grupo ya cuenta con una distribución asignada');
        return redirect()->back();
      }
      \App\dist_grupos::create(
        [
          "dist_id" => $r->dist_id,
          "grupo" => $r->grupo,
        ]
      );

      $r->session()->put('status', 'Grupo asignado');
      return redirect()->back();
    }
    public function delgrupo(Request $r){
      \App\dist_grupos::find($r->id)->delete();
      $r->session()->put('status', 'Grupo eliminado');
      return redirect()->back();
    }
    public function delconcepto(Request $r){
      \App\conceptosdist::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }

    public function nuevoconcepto(Request $r){
      if($r->concepto != ""){
        \App\conceptosdist::create([
          "concepto" => $r->concepto,
          "tipo" => $r->tipo,
          "conceptopago" => $r->conceptopago,
          "cantidad" => $r->cantidad,
          "opcional" => $r->opcional,
          "distribucion_id" => $r->dist_id
        ]);
      }
      $r->session()->put('status', 'Concepto creado');
      return redirect()->back();
    }
}

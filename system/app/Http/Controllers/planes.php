<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class planes extends Controller
{
    public function setall(Request $r){
      $planes = \App\alumnos_planes::whereIn("matricula",$r->planes)->get();
      $planes->each(function($item) use($r){
        $item->update([
          "since" => $r->since,
          "every" => $r->every,
          "beca" => $r->beca
        ]);
      });
      \Session::put("status","Planes modificados");
      return redirect()->back();
    }
    public function setfecha(Request $r){
      if(\App\alumnos_planes::whereRAW("md5(id)='$r->cid'")->first()->update([
        "since" => $r->since
      ])){
        return "ok";
      }
    }
    public function setdias(Request $r){
      if(\App\alumnos_planes::whereRAW("md5(id)='$r->cid'")->first()->update([
        "every" => $r->every
      ])){
        return "ok";
      }
    }
    public function setbeca(Request $r){
      if(\App\alumnos_planes::whereRAW("md5(id)='$r->cid'")->first()->update([
        "beca" => $r->beca
      ])){
        return "ok";
      }
    }

    public function actualizar(Request $r){
      if(\App\alumnos_planes::whereRAW("md5(id)='$r->cid'")->first()->update([
        "since" => $r->since,
        "every" => $r->every
      ])){
        return "ok";
      }
    }
    public function asignarpago(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      if(\App\planes_pagos::create([
        "alumno_plan_id" => $r->plan_id,
        "pago_id" => $pago->id,
      ])){
        return "ok";
      }
    }
}

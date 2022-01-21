<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class facturas extends Controller
{
  public function solicitar(Request $r){
    if(\App\facturas::create(["pago_id"=>$r->folio])){
      $data["status"] = 1;
    } else {
      $data["status"] = 0;
    }
    return json_encode($data);
  }

  public function solicitarfactura(Request $r){
    $pago = \App\pagos::where("id",$r->folio)->first();
    $alumno = $pago->alumno;
    $facturacion = $pago->facturacion;
    $datos = $r->all();


    unset($datos["_token"]);
    unset($datos["folio"]);
    unset($datos["factura"]);
    unset($datos["nivelpath"]);
    unset($datos["nivel"]);

    if(isset($alumno)){
      $datos["alumno_id"] = $alumno->id;
    } else {
      $datos["folio_id"] = $r->folio;
    }
    if($facturacion == null){
      $facturacion = \App\facturacion::create($datos);
    }
    $facturacion->update($datos);

    \App\facturas::create(["pago_id"=>$r->folio]);

    return redirect()->back()->with("status","Factura marcada como entregada");

  }

  public function check(Request $r){
    $fact = \App\facturas::whereRAW("md5(id)='$r->cid'")->first();
    $fact->status = 1;
    $fact->save();
    return redirect()->back()->with("status","Factura marcada como entregada");
  }

  public function uncheck(Request $r){
    $fact = \App\facturas::whereRAW("md5(id)='$r->cid'")->first();
    $fact->status = NULL;
    $fact->save();
    return redirect()->back()->with("status","Factura marcada como NO entregada");
  }
}

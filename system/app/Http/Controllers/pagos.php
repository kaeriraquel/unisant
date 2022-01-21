<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class pagos extends Controller
{
    public function disableplan(Request $r){
      $plan = \App\planespago::whereRAW("md5(id)='".$r->cid."'")->first();
      $plan->disable = 1;
      $plan->save();

      $r->session()->put('status', 'Plan desactivado');
      return redirect("/pagos/planesdepago");
    }
    public function enableplan(Request $r){
      $plan = \App\planespago::whereRAW("md5(id)='".$r->cid."'")->first();
      $plan->disable = null;
      $plan->save();

      $r->session()->put('status', 'Plan activado');
      return redirect()->back();
    }
    public function plandepago(Request $r){
      \App\planespago::create([
        "concepto" => $r->concepto,
        "monto" => $r->monto,
        "plazo" => $r->plazo,
        "concepto_id" => $r->concepto_id
      ]);

      $r->session()->put('status', 'Plan creado');
      return redirect("/pagos/planesdepago");
    }
    public function acfecha(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $p->update(["fecha_pago"=>$r->fecha]);
      return "ok";
    }
    public function delconcepto(Request $r){
      //\App\conceptospago::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }
    public function switchconcepto(Request $r){
      $c = \App\conceptospago::whereRAW("md5(id)='".$r->cid."'")->first();
      $c->allow = ($c->allow == NULL) ? 1 : NULL;
      $c->save();
      return "ok";
    }
    public function gencount(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $p->gen_count = $p->gen_count + 1;
      $p->save();
      return "ok";
    }
    public function deldivisa(Request $r){
      \App\divisas::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }
    public function delpas(Request $r){
      \App\pasarelas::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }
    public function getpasarelas(Request $r){
      return json_encode(\App\pasarelas::where("forma_pago",$r->forma)->get());
    }
    public function deldistri(Request $r){
      \App\distribuciones::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }
    public function nuevoconcepto(Request $r){
      if($r->concepto != ""){
        \App\conceptospago::create([
          "concepto" => $r->concepto
        ]);
        $data["ok"] = "true";
      } else {
        $data["statusText"] = "El concepto no puede estar vacio";
      }
      $r->session()->put('status', 'Concepto creado');
      return json_encode($data);
    }
    public function nuevapasarela(Request $r){
      \App\pasarelas::create([
        "name" => $r->name,
        "comision" => $r->comision,
        "fijo" => $r->fijo,
        "forma_pago" => $r->forma_pago,
        "iva" => $r->iva,
      ]);
      $r->session()->put('status', 'Pasarela agregada');
      return redirect("/pagos/pasarelas");
    }
    public function nuevadivisa(Request $r){
      if($r->divisa != ""){
        \App\divisas::create([
          "divisa" => $r->divisa
        ]);
        $data["ok"] = "true";
      } else {
        $data["statusText"] = "La divisa no puede estar vacia";
      }
      $r->session()->put('status', 'Distribución creado');
      return json_encode($data);
    }
    public function nuevadistri(Request $r){
      if($r->distri != ""){
        \App\distribuciones::create([
          "distribucion" => $r->distri
        ]);
        $data["ok"] = "true";
      } else {
        $data["statusText"] = "La distribución no puede estar vacia";
      }
      $r->session()->put('status', 'Distribución creada');
      return json_encode($data);
    }

    public function guardar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

        ini_set('upload_max_filesize', '2G');
        ini_set('post_max_size', '4G');
        ini_set('max_execution_time', '5000000');
        ini_set('max_input_time', '5000000');
        ini_set('memory_limit', '200M');

        $document_id = 0;

        if($r->hasFile('file')){
          $file = $r->file('file');
          $data = explode('.',$file->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
           $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = \App\documentos::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>rand(),'usuario_id'=>\Auth::user()->id]);
          $file->move(storage_path()."/comprobantes/",md5($document->id).'.file');
          $document_id = $document->id;
        }


        $id = base64_decode($params->cid);


        $sede = (isset(\Auth::user()->sede)) ? \Auth::user()->sede : \App\sedes::where('sede','Administrador')->first();

        $current_count = $sede->sede->factura_count ?: 0;
        $current_count++;

        $pok = \App\pagos::create(
          [
            "folio_impreso"=>$r->folio_impreso,
            "fecha_pago"=>$r->fecha_pago,
            "concepto"=>$r->concepto.": ".$r->conceptoadicional,
            "sede_folio"=>$current_count,
            "sede_id"=>$sede->id,
            "pasarela_id"=>$r->pasarela_id,
            "clave"=>$r->clave,
            "monto"=>$r->monto,
            "matricula"=>$id,
            "nota"=>$r->nota,
            "document_id"=>$document_id
          ]);

        if($r->has("extra")){
          $pok->extra = $r->extra;
          $pok->metodo = $r->metodo;
          $pok->save();
        }

        if($r->has("cantidad_pagos")){
          $pok->cantidad_pagos = $r->cantidad_pagos;
          $pok->save();
        }

        if($pok){
          $sede->sede->factura_count = $current_count;
          $sede->sede->save();
        }

        if(isset($r->plan_id)){
          $plan_pago = \App\planes_pagos::create([
            "alumno_plan_id" => $r->plan_id,
            "pago_id" => $pok->id
          ]);
          $plan_pago->alumno_plan->monto_restante -= $pok->monto;
          $plan_pago->alumno_plan->save();
        }

        $r->session()->put('status', 'Pago realizado');
        return redirect("/alumnos/pagos?cid=".$params->cid."#pagos");
    }
    public function comopendientes(Request $r){
      \App\pagos::where("estado",NULL)->update(["estado"=>1]);
      return redirect()->back()->with("status","Todos los pagos sin cobrar han sido marcados como pendientes");
    }
    public function eliminar(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      $p->deleted_at = \Carbon\carbon::now();
      $p->razon = $r->razon;
      $p->save();
      \App\planes_pagos::where("pago_id",$p->id)->delete();
      return \Redirect::to(\URL::previous() . "#pagos")->with("status","Pago eliminado");
    }
    public function devolver(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      $p->returned_at = \Carbon\carbon::now();
      $p->save();
      \App\planes_pagos::where("pago_id",$p->id)->delete();
      return \Redirect::to("/conciliaciones/requerimientos?cid=".md5($p->conciliacion_id). "&pago=".md5($p->id))->with("status","Pago devuelto");
    }
    public function iniciardevol(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      $p->returned_at = \Carbon\carbon::now();
      $p->estado = 10;
      $p->save();
      return \Redirect::to(\URL::previous())->with("status","Proceso iniciado");
    }
    public function cancelardev(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      $p->returned_at = NULL;
      $p->estado = NULL;
      $p->save();
      return \Redirect::to(\URL::previous())->with("status","Solicitud denegada");
    }
    public function autorizardev(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      $p->returned_at = \Carbon\carbon::now();
      $p->estado = 1;
      \App\planes_pagos::where("pago_id",$p->id)->delete();
      $p->save();
      return \Redirect::to(\URL::previous())->with("status","Solicitud autorizada");
    }
    public function unlink(Request $r){
      $p = \App\pagos::whereRAW("md5(id)='$r->cid'")->first();
      \App\planes_pagos::where("pago_id",$p->id)->delete();
      return "ok";
    }
    public function distribuir(Request $r){
      \App\pagos::whereRAW("md5(sede_id)='$r->cid'")->where("estado",1)->update(["estado"=>2]);
      return redirect()->back()->with("status","Se ha distribuido el pago a la sucursal");
    }
    public function descargar(Request $r, $code)
    {
      $file = \App\documentos::whereRAW("md5(id)='$code'")->first();
      return \Response::download(storage_path("/comprobantes/".$code.'.file'),$file->titulo.'.'.$file->ext);
    }

    public function ver(Request $r, $code)
    {
      $file = \App\documentos::whereRAW("md5(id)='$code'")->first();
      return \Response::file(storage_path("/comprobantes/".$code.'.file'));
    }
}

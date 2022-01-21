<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class conciliaciones extends Controller
{
    public function nueva(Request $r){
      if(count($r->pagos) > 0){
        $c = \App\conciliaciones::create(
          [
            "nota"=>$r->nota,
            "desde"=>$r->desde,
            "hasta"=>$r->hasta,
            "concepto"=>$r->concepto,
            "sede_id"=>\Auth::user()->sede->sede->id
          ]);
        $pagos = \App\pagos::findMany($r->pagos);
        $pagos->each(function($i) use ($c){
          $i->update(["conciliacion_id"=>$c->id,"estado"=>1]);
        });
        return redirect("/")->with("status","Conciliación enviada");
      } else {
        \Session::put("error","Debes seleccionar al menos un pago a conciliar");
        return redirect()->back();
      }
    }
    public function switchconcepto(Request $r){
      $concepto = \App\conciliaciones_conceptos::whereRAW("md5(id)='".$r->cid."'")->first();
      if($concepto->show_sede == NULL){
        $concepto->show_sede = 1;
      } else {
        $concepto->show_sede = NULL;
      }
      $concepto->save();
      \Session::put("status","Visibilidad de concepto cambiada");
      return redirect()->back();
    }
    public function deshacer(Request $r){
      $c = \App\conciliaciones::whereRAW("md5(id)='".$r->cid."'")->first();
      $c->pagos->each(function($i) use ($c){
        $i->update(["conciliacion_id"=>null,"estado"=>null]);
      });
      $c->requerimientos->each(function($i) use ($c){
        if ($r->document_id != NULL) {
          unlink(storage_path()."/comprobantes/",md5($document->id).'.file');
        }
        $i->delete();
      });
      $c->delete();
      \Session::put("status","Concialiación deshecha");
      return redirect("/conciliaciones/lista");
    }
    public function deshacerad(Request $r){
      $c = \App\conciliaciones::whereRAW("md5(id)='".$r->cid."'")->first();
      $c->pagos->each(function($i) use ($c){
        $i->update(["conciliacion_id"=>null,"estado"=>null]);
      });
      $id = $r->document_id;
      $c->requerimientos->each(function($i) use ($c, $id){
        if ($id != NULL) {
          unlink(storage_path()."/comprobantes/",md5($id).'.file');
        }
        $i->delete();
      });
      $c->terms->each(function($i) use ($c, $id){
        $i->delete();
      });
      $c->delete();
      \Session::put("status","Conciliación deshecha");
      return redirect("/home");
    }
    public function requerimiento(Request $r){
      $c = \App\conciliaciones::whereRAW("md5(id)='$r->cid'")->first();
      $document_id = null;
      if($r->hasFile('file')){
        $file = $r->file('file');
        $data = explode('.',$file->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
         $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $document = \App\documentos::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'usuario_id'=>\Auth::user()->id]);
        $file->move(storage_path()."/comprobantes/",md5($document->id).'.file');
        $document_id = $document->id;
      }
      $r = \App\requerimientos::create(["sede_id"=>$c->sede_id,"document_id"=>$document_id,"conciliacion_id"=>$c->id,"monto"=>$r->monto,"concepto"=>$r->concepto]);
      \Session::put("status","Requerimiento almacenado");
      return redirect()->back();
    }
    public function eliminarreq(Request $r){
      $c = \App\requerimientos::whereRAW("md5(id)='$r->cid'")->delete();
      \Session::put("status","Requerimiento eliminado");
      return redirect()->back();
    }
    public function conciliar(Request $r){
      $c = \App\conciliaciones::whereRAW("md5(id)='$r->cid'")->first();
      $c->estado =  1;
      $c->save();
      $conceptos = $r->all();
      unset($conceptos["_token"]);
      unset($conceptos["cid"]);
      unset($conceptos["nivel"]);
      unset($conceptos["nivelpath"]);

      foreach ($conceptos as $key => $value) {
        \App\conciliaciones_conceptos::create(
          [
            "keyval" => $key,
            "value" => $value,
            "conciliacion_id" => $c->id
          ]
        );
      }
      \Session::put("status","Conciliación realizada");
      return redirect("/conciliaciones/requerimientos?cid=$r->cid");
    }
    public function desconciliar(Request $r){
      $c = \App\conciliaciones::whereRAW("md5(id)='$r->cid'")->first();
      $c->estado =  NULL;
      $c->save();

      \App\conciliaciones_conceptos::where("conciliacion_id",$c->id)->delete();

      return "ok";
    }
}

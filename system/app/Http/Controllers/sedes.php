<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class sedes extends Controller
{
    public function setaccess(Request $r){
      $usertologin = \App\sede_usuario::where("sede_id",$r->sede_id)->first();
      if($usertologin == null)
        return redirect()->back()->with("error","No hay usuarios para logearse en esta sede");
      session()->put("icomefrom",md5(auth()->user()->id));
      auth()->login($usertologin->usuario);
      return redirect("/alumnos/listaest")->with("status","Acceso a sede concedido");
    }
    public function retireaccess(Request $r){
      $usertologin = \App\User::whereRAW("md5(id)='".session()->get("icomefrom")."'")->first();
      session()->remove("icomefrom");
      auth()->login($usertologin);
      $r->session()->put('status', 'Has vuelto a ser tu!');
      return redirect("/alumnos/listaest");
    }
    public function addaccess(Request $r){
      \App\accesos_sedes::create(
        [
          "owner_id" => $r->owner_id,
          "sede_id" => $r->sede_id
        ]);
      return redirect()->back()->with("status","Acceso concedido");
    }
    public function delaccess(Request $r){
      \App\accesos_sedes::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return redirect()->back()->with("status","Acceso eliminado");
    }
    public function copyconceptofrom(Request $r){
      $sedes = \App\conceptos_sedes::where("sede_id",$r->sede_id)->get();
      foreach ($sedes as $cs) {
        \App\conceptos_sedes::create([
          "sede_id" => $r->cid,
          "concepto_id"=> $cs->concepto_id
        ]);
      }
      return redirect()->back()->with("status","Conceptos copiados");
    }
    public function guardar(Request $r){
      \App\sedes::create(["sede"=>$r->sede])->save();
      return redirect()->back()->with("status","Sede almacenada");
    }

    public function del(Request $r){
      \App\sedes::whereRAW("md5(id)='$r->cid'")->first()->delete();
      \Session::put("status","Sede eliminada");
      return redirect()->back();
    }

    public function addplan(Request $r){
      \App\planes_sedes::create([
        "plan_id" => $r->planes,
        "sede_id" => $r->cid
      ]);

      return redirect()->back()->with("status","Plan agregado");
    }
    public function addconcepto(Request $r){
      \App\conceptos_sedes::create([
        "sede_id" => $r->cid,
        "concepto_id" => $r->concepto_id
      ]);

      return redirect()->back()->with("status","Concepto asignado");
    }
    public function delplan(Request $r){
      \App\planes_sedes::find($r->id)->delete();

      return redirect()->back()->with("status","Plan eliminado");
    }
    public function delconcepto(Request $r){
      \App\conceptos_sedes::find($r->id)->delete();

      return redirect()->back()->with("status","Concepto eliminado");
    }

    public function guardarkey(Request $r){
      \App\keys::create(["key"=>$r->key,"value"=>$r->value])->save();
      return redirect()->back()->with("status","Key almacenada");
    }

    public function actualizarkey(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $key = \App\keys::whereRAW("md5(id)='$params->cid'")->first();
      $key->key = $r->key;
      $key->value = $r->value;
      $key->save();
      return redirect()->back()->with("status","Key actualizada");
    }

    public function actualizarmonto(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;
      $mat = base64_decode($params->cid);

      if ($mat != $r->clave_alumno) {
        if(\App\alumnosest::where("matricula",$r->clave_alumno)->count() > 0)
        {
          $r->session()->put("link","/alumnos/pagos?cid=".base64_encode($r->clave_alumno));
          $r->session()->put('error', "La matrícula $r->matricula se encuentra ocupada");
          return redirect()->back();
        } else {
          // Actualiza mat
          \App\montos::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);
          \App\grupos::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);
          \App\pagos::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);
          \App\nombres::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);
          \App\alumnos_planes::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);

          \App\alumnosest::where("matricula",$mat)->update(["matricula"=>$r->clave_alumno]);

          $mat = $r->clave_alumno;
          $x = 0;
        }
      }

      $monto = \App\montos::where("matricula",$mat)->first();
      $monto->porcentaje_materia = $r->porcentaje_materia;
      $monto->save();

      $grupo = null;
      if($r->grupo != "Sin grupo"){
        $grupo = \App\grupos::where("matricula",$mat)->first();
        if($grupo == null){
          $grupo = \App\grupos::create(["alumnoest_id"=>0,"grupo"=>$r->grupo,"matricula"=>$mat]);
        }
      }
      if($grupo != null){
        $grupo->grupo = $r->grupo;
        $grupo->save();
      }
      $udata = [
        "matricula"=>$r->clave_alumno,
        "nombre_completo"=>$r->nombre,
        "apat"=>$r->apat,
        "amat"=>$r->amat,
        "curp"=>$r->curp,
        "fecha_nacimiento"=>$r->fecha_nacimiento,
        "fecha_registro"=>$r->fecha_registro,
        "grado"=>$r->grado,
        "grupo_"=>$r->grupo_,
        "periodo_id"=>$r->periodo_id,
      ];

      if($r->sede_id != null)
        $udata["sede_id"] = $r->sede_id;

      if($r->estado_alumno != null)
        $udata["baja"] = $r->estado_alumno;

      $alumno = \App\alumnosest::where("matricula",$mat)->first();
      if($alumno != NULL)
        $alumno->update($udata);

      \Session::put("status","Cambios efectuados");
      return redirect("/alumnos/pagos?cid=".base64_encode($mat).($r->has("did") ? "&did=".$r->did : ""));
    }

    public function actualizar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $s = \App\sedes::whereRAW("md5(id)='$params->cid'")->first();

      $s->sede = $r->sede;
      $s->todos = $r->todos;
      $s->monto = $r->monto;
      $s->individual = $r->individual;
      $s->divisa_id = $r->divisa;

      $s->save();

      return redirect()->back()->with("status","Sede actualizada");
    }
}

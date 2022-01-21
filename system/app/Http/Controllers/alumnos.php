<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class alumnos extends Controller
{
    public function back(Request $r){
      return redirect()->back();
    }

    public function addcustomplan(Request $r){
      $concepto = $r->concepto_id;
      $planes = \App\alumnos_planes::where("matricula",base64_decode($r->cid))
      ->where("disable",NULL)->whereHas("plan",function($query) use ($concepto){
        $query->where("concepto_id",$concepto);
      })->count();

      if ($planes > 0) {
        $r->session()->put('error', 'Este alumno ya cuenta con un plan bajo el mismo concepto activo.');
      } else {
        $plan = \App\planespago::create([
          "concepto" => $r->concepto,
          "monto" => $r->monto,
          "plazo" => $r->plazo,
          "concepto_id" => $r->concepto_id,
          "disable" => 1
        ]);

        $pa = \App\alumnos_planes::create([
          "matricula" => base64_decode($r->cid),
          "plan_id" => $plan->id
        ]);

        $pa->monto_restante = $pa->plan->monto;
        $pa->save();
      }

      $r->session()->put('status', 'Plan individual creado');
      return redirect("/alumnos/planes?cid=".$r->cid);
    }

    public function addplanes(Request $r){
      $concepto = \App\planespago::find($r->plan_id)->concepto_id;
      $planes = \App\alumnos_planes::where("matricula",base64_decode($r->cid))
      ->where("disable",NULL)->whereHas("plan",function($query) use ($concepto){
        $query->where("concepto_id",$concepto);
      })->count();

      if($planes > 0){
        $r->session()->put('error', 'Este alumno ya cuenta con un plan bajo el mismo concepto activo.');
      } else {
        $pa = \App\alumnos_planes::create([
          "matricula" => base64_decode($r->cid),
          "plan_id" => $r->plan_id
        ]);
        $pa->monto_restante = $pa->plan->monto;
        $pa->save();
        $r->session()->put('status', 'Plan añadido creado');
      }

      return redirect("/alumnos/planes?cid=".$r->cid);
    }

    public function delplanpagos(Request $r){
      \App\alumnos_planes::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }

    public function detplanpagos(Request $r){
      $det = \App\alumnos_planes::whereRAW("md5(id)='".$r->cid."'")->first();
      $det->disable = 1;
      $det->save();
      return "ok";
    }

    public function eliminar(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $mat = base64_decode($params->cid);

      $alumno = \App\alumnosest::where("matricula",$mat)->first();
      if($alumno){
        if($alumno->pagos != null){
          $alumno->pagos->delete();
        }
        if($alumno->grupo != null){
          $alumno->grupo->delete();
        }
        if(\App\montos::where("matricula",$mat)->first() != null){
          \App\montos::where("matricula",$mat)->first()->delete();
        }
        if($alumno->iadicional != null){
          $alumno->iadicional->delete();
        }
        if ($alumno->datosacademicos != null) {
          $alumno->datosacademicos->delete();
        }
        if ($alumno->facturacion != null) {
          $alumno->facturacion->delete();
        }
        $alumno->delete();

        $r->session()->put('status', "Alumno eliminado");
        return redirect("/alumnos/listaest");
      } else {
        return redirect()->back()->with("error","El alumno no existe");
      }
    }

    public function guardarest(Request $r){
      $datos = $r->all();
      $fact = [
        "razonsocial" => $r->razonsocial,
        "rfc" => $r->rfc,
        "codigopostal" => $r->codigopostal,
        "numeroexterior" => $r->numeroexterior,
        "numerointerior" => $r->numerointerior,
        "estado" => $r->estado,
        "alcaldia" => $r->alcaldia,
        "colonia" => $r->colonia
      ];

      unset($datos["_token"]);
      unset($datos["nivel"]);
      unset($datos["grupo"]);
      unset($datos["nivelpath"]);
      unset($datos["razonsocial"]);
      unset($datos["rfc"]);
      unset($datos["codigopostal"]);
      unset($datos["numeroexterior"]);
      unset($datos["numerointerior"]);
      unset($datos["estado"]);
      unset($datos["alcaldia"]);
      unset($datos["colonia"]);


      if(\App\alumnosest::where("matricula",$r->matricula)->count() > 0){
        $r->session()->put("link","/alumnos/pagos?cid=".base64_encode($r->clave_alumno));
        $r->session()->put('error', "La matrícula $r->matricula ya se encuentra registrada $link");
        return redirect("/alumnos/nuevoest");
      }

      if(\App\alumnosest::where("curp",$r->curp)->count() > 0){
        $r->session()->put('error', "La CURP $r->curp ya se encuentra registrada");
        return redirect("/alumnos/nuevoest");
      }

      $a = \App\alumnosest::create($datos);
      if($a != null){
        $fact["alumno_id"] = $a->id;
        \App\facturacion::create($fact);
      }

      if(!empty($r->grupo)){
        \App\grupos::create(["matricula"=>$r->matricula,"grupo"=>$r->grupo,"alumnoest_id"=>$a->id]);
      }
      $r->session()->put('status', 'Alumno creado');
      return redirect("/alumnos/listaest");
    }

    public function informacionadicional(Request $r){
      $all = $r->all();
      unset($all["_token"]);
      unset($all["nivel"]);
      unset($all["nivelpath"]);

      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      if(isset($r->id)){
        \App\iadicional::find($r->id)->update($all);
        $r->session()->put('status', 'Información adicional actualizada');
      } else {
        \App\iadicional::create($all);
        $r->session()->put('status', 'Información adicional agregada');
      }

      return redirect("/alumnos/pagos?cid=".$params->cid);
    }

    public function academicos(Request $r){
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
          $document = \App\documentos::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'usuario_id'=>\Auth::user()->id]);
          $file->move(storage_path()."/comprobantes/",md5($document->id).'.file');
          $document_id = $document->id;
        }

        \App\antecedentes::create([
            "file" => $document_id,
            "fecha_termino" => $r->fecha_termino,
            "cedula" => $r->cedula,
            "alumno_id" => $r->id
        ]);
        $r->session()->put('status', 'Datos académicos creados');
        return redirect("/alumnos/pagos?cid=".$params->cid);
    }

    public function baja(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $alumno = \App\alumnosest::whereRAW("matricula='".base64_decode($params->cid)."'")->first();
      $alumno->baja = 1;
      $alumno->save();

      return redirect()->back()->with("status","Baja de alumno solicitada");
    }

    public function alta(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $alumno = \App\alumnosest::whereRAW("matricula='".base64_decode($params->cid)."'")->first();
      $alumno->baja = NULL;
      $alumno->save();

      return redirect()->back()->with("status","Alta de alumno");
    }
    public function revertir(Request $r){
      $parsedUrl = parse_url(\URL::previous());
      parse_str($parsedUrl['query'], $output);
      $params = (object) $output;

      $alumno = \App\alumnosest::whereRAW("matricula='".base64_decode($params->cid)."'")->first();
      $alumno->baja = NULL;
      $alumno->save();

      return redirect()->back()->with("status","Proceso de baja Cancelado");
    }
    public function cancelar(Request $r){
      $alumno = \App\alumnosest::whereRAW("matricula='".base64_decode($r->cid)."'")->first();
      $alumno->baja = NULL;
      $alumno->save();

      return redirect()->back()->with("status","Proceso de baja Cancelado");
    }
    public function darbaja(Request $r){
      $alumno = \App\alumnosest::whereRAW("matricula='".base64_decode($r->cid)."'")->first();
      $alumno->baja = 2;
      $alumno->save();

      return redirect()->back()->with("status","Baja autorizada");
    }
    public function addmateria(Request $r){
      $alumno = \App\alumnosest::whereRAW("md5(id)='".$r->cid."'")->first();
      $alumno_materia = [
        "materia_id" => $r->materia_id,
        "alumno_id" => $alumno->id,
        "calificacion" => $r->calificacion,
        "periodo_id" => $r->periodo_id
      ];

      if($r->has("reprobada_id")){
        $alumno_materia["reprobada_id"] = $r->reprobada_id;
      }

      \App\alumnos_materias::create($alumno_materia);

      \Session::put("status","Materia añadida");
      return redirect()->back();
    }
    public function delmateria(Request $r){
      \App\alumnos_materias::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
      return "ok";
    }
}

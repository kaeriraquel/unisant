<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class controlescolar extends Controller
{
    public function activarproceso(Request $r){
      \App\actas::create(["matricula"=>$r->matricula,"avance"=>10]);
      \App\certificados::create(["matricula"=>$r->matricula,"avance"=>10]);
      \App\titulos::create(["matricula"=>$r->matricula,"avance"=>10]);

      \Session::put("status","Proceso activado exitosamente");
      return redirect()->back();
    }

    public function addparam(Request $r){
      \App\parametros::create([
        "name" => $r->name,
        "fora" => $r->fora,
        "type" => $r->type,
        "sede_id" => auth()->user()->sede->sede->id
      ]);
      \Session::put("status","Parámetro agregado");
      return redirect()->back();
    }

    public function delparam(Request $r){
      \App\parametros::whereRAW("md5(id)='$r->cid'")->delete();
      return "ok";
    }

    public function delprocess(Request $r){
      \App\actas::where("matricula",$r->matricula)->first()->delete();
      \App\certificados::where("matricula",$r->matricula)->first()->delete();
      \App\titulos::where("matricula",$r->matricula)->first()->delete();

      $prevs = \App\valores_parametros::where("matricula",$r->matricula)->get();
      $prevs->each(function($item){
        $item->delete();
      });
      return "ok";
    }

    public function actualizarproceso(Request $r){
      \App\actas::where("matricula",$r->matricula)->update(
        ["avance"=>$r->avance_acta,"estado"=>$r->estado_acta]
      );
      \App\certificados::where("matricula",$r->matricula)->update(
        ["avance"=>$r->avance_certificado,"estado"=>$r->estado_certificado]
      );
      \App\titulos::where("matricula",$r->matricula)->update(
        ["avance"=>$r->avance_titulo,"estado"=>$r->estado_titulo]
      );

      $all = $r->all();
      $campos = [];
      foreach ($all as $key => $value) {
        if(strstr($key,"campo")){
          $id = str_replace("campo_","",$key);
          array_push($campos,["parametro_id"=>$id,"matricula"=>$r->matricula,"value"=>$value]);
        }
      }

      $prevs = \App\valores_parametros::where("matricula",$r->matricula)->get();

      foreach($campos as $param){
        \App\valores_parametros::create($param);
      }

      $prevs->each(function($item){
        $item->delete();
      });

      \Session::put("status","Proceso actualizado");
      return redirect()->back();
    }

    public function descargar(Request $r, $code, $ext)
    {
      return \Response::download(storage_path("/apoyo/".$code.".$ext"),$code.".$ext");
    }

    public function limpiarimportados(Request $r){
      \App\materiasimportadas::truncate();
      \Session::put("status","Documento importado deshechado");
      return redirect()->back();
    }

    public function deshaceralumnosimportados(Request $r){
      \App\alumnosimportados::truncate();
      \Session::put("status","Documento importado deshechado");
      return redirect()->back();
    }

    public function continuaralumnos(Request $r){
      $alumnos = \App\alumnosimportados::where("insertar",true)->get();
      $i = 0;
      $count = 0;
      $countbad = 0;

      foreach ($alumnos as $alu) {
        try{
          $alumno = [
            "nombre_completo" => strtoupper($alu->nombre),
            "apat" => strtoupper($alu->apat),
            "grupo_" => $alu->grupo,
            "grado" => $alu->grado,
            "amat" => strtoupper($alu->amat),
            "sede_id" => $alu->sede,
            "revoe_id" => $alu->rvoe,
            "periodo_id" => $alu->periodo,
            "matricula" => $alu->matricula,
            "baja" => intval($alu->status),
            "curp" => $alu->curp,
            "genero_biologico" => strtoupper($alu->genero),
            "fecha_nacimiento" => \Carbon\carbon::parse($alu->fecha_nacimiento),
            "fecha_inscripcion" => \Carbon\carbon::parse($alu->fecha_inscripcion),
            "fecha_registro" => \Carbon\carbon::parse($alu->fecha_registro)
          ];
          $_alumno = \App\alumnosest::create($alumno);
          if($_alumno != NULL){
            $iadicional = [
              "calle" => $alu->calle,
              "numero" => $alu->numero,
              "colonia" => $alu->colonia,
              "cp" => $alu->cp,
              "municipio" => $alu->municipio,
              "estado_residencia" => $alu->estado,
              "correo_electronico" => $alu->email,
              "celular" => $alu->celular,
              "telefono" => $alu->telefono,
              "alumno_id" => $_alumno->id
            ];
            \App\iadicional::create($iadicional);

            \App\grupos::create([
              "grupo"=>$alu->grupo_distribucion,
              "matricula"=>$alu->matricula,
              "alumnoest_id"=>$_alumno->id
            ]);

            $count++;
          }

        } catch(Exception $exception){
          $countbad++;
        }
      }

      \App\alumnosimportados::truncate();

      \Session::put("status","Documento importado, $count importados, $countbad omitidos.");
      return redirect()->back();
    }

    public function continuarimportacion(Request $r){
      $materias = \App\materiasimportadas::all();
      $i = 0;
      $count = 0;
      $countbad = 0;
      foreach ($materias as $mat) {
        if(is_numeric($mat->periodo_id)){
          $fecha = $mat->periodo->periodo;
        }  else {
          try {
            $fecha = \Carbon\carbon::parse($mat->periodo_id)->format("Y-m-d");
          } catch (\Exception $e) {
            $fecha = null;
          }

        }
        if ($mat->alumno != NULL
          && $mat->materia != NULL
          && $fecha != NULL
          && (($mat->calificacion <= 5 && $mat->tiporeprobada != NULL) || $mat->calificacion > 5)) {

          $materia[$i++] = [
              "alumno_id" => $mat->alumno->id,
              "materia_id" => $mat->materia_id,
              "periodo_id" => $mat->periodo_id,
              "calificacion" => $mat->calificacion,
              "reprobada_id" => $mat->tiporeprobada_id
             ];
        }
      }

      //dd($materia);

      foreach ($materia as $mat) {
        try{
          $a = \App\alumnos_materias::create($mat);
          $count++;
        } catch(Exception $exception){
          $countbad++;
        }
      }

      \App\materiasimportadas::truncate();

      \Session::put("status","Documento importado, $count importados, $countbad omitidos.");
      return redirect()->back();
    }

    public function importaralumnos(Request $r){
      if($r->has("chooseFile")){
        $file = $r->file('chooseFile');
        $name = explode('.',$file->getClientOriginalName());
        if(strtolower($name[1]) == strtolower("csv")){
          if(strtolower($name[0]) == strtolower("alumnos")){
            //
            $actuales = \App\alumnosimportados::all();

            $data =  $file->get();
            $rows = explode("\n",$data);
            $rows_re = array_reverse($rows);
            $corte = false;
            $i = 0;
            $count = 0;
            $countbad = 0;
            $complex = ["matricula","nombre","apat","amat","grado",
            "grupo","periodo","rvoe","sede","grupo_distribucion",
            "curp","fecha_nacimiento","fecha_inscripcion","fecha_registro","genero",
            "telefono","celular","email","calle","numero","colonia",
            "cp","municipio","estado","status"];

            $importadas = [];
            foreach ($rows_re as $colValue) {
              $col = explode(",",$colValue);
              $duplex = [];
              $aydis = [6,7,8,24];
              foreach ($col as $key => $colum) {
                $val = $colum;
                if ($colum != "" && in_array($key,$aydis))
                  $val = substr($val,4);
                if($colum == "")
                  $val = 0;
                if(!isset($complex[$key])){
                  \Session::put("error","El documento no es válido, posiblemente contiene , en la tupla ".$duplex[$complex[0]]);
                  return redirect()->back();
                }
                $duplex[$complex[$key]] = utf8_encode ($val);;
              }
              $importadas[$i++] = $duplex;
            }

            unset($importadas[count($importadas)-1]);

            foreach ($importadas as $alu) {
              try{
                $a = \App\alumnosimportados::create($alu);
                $count++;
              } catch(\Illuminate\Database\QueryException $exception){
                $countbad++;
              }
            }

            $actuales->each(function($alu){
              $alu->delete();
            });

            \Session::put("status","Documento importado, $count importados, $countbad omitidos.");
            return redirect()->back();
            //
          } else {
            \Session::put("error","El documento debe tener por nombre: alumnos");
            return redirect()->back();
          }
        } else {
          \Session::put("error","El documento debe ser CSV.");
          return redirect()->back();
        }
      } else {
        \Session::put("error","Debes anexar un documento CSV válido.");
        return redirect()->back();
      }
    }

    public function importar(Request $r){

      if($r->has("chooseFile")){
        $file = $r->file('chooseFile');
        $name = explode('.',$file->getClientOriginalName());
        if(strtolower($name[1]) == strtolower("csv")){
          if(strtolower($name[0]) == strtolower("calificaciones")){
            //
            $actuales = \App\materiasimportadas::all();

            $data =  $file->get();
            $rows = explode("\n",$data);
            $rows_re = array_reverse($rows);
            $corte = false;
            $i = 0;
            $count = 0;
            $countbad = 0;
            $complex = ["matricula","materia_id","calificacion","tiporeprobada_id","periodo_id"];
            $importadas = [];
            foreach ($rows_re as $colValue) {
              $col = explode(",",$colValue);
              $duplex = [];
              foreach ($col as $key => $colum) {
                $val = str_replace("\r","",$colum);
                if ($colum != "" && ($key == 1 || $key == 3))
                  $val = substr($val,4);

                if($colum != "" && $key == 4 && is_numeric($val)){
                    $val = intval(substr($val,4));
                }
                if($val == "")
                  $val = 0;
                $duplex[$complex[$key]] = $val;
              }
              $importadas[$i++] = $duplex;
            }

            //dd($importadas);

            foreach ($importadas as $mat) {
              try{
                $a = \App\materiasimportadas::create($mat);
                $count++;
              } catch(\Illuminate\Database\QueryException $exception){
                $countbad++;
              }
            }

            $actuales->each(function($mat){
              $mat->delete();
            });

            \Session::put("status","Documento importado, $count importados, $countbad omitidos.");
            return redirect()->back();
            //
          } else {
            \Session::put("error","El documento debe tener por nombre: calificaciones");
            return redirect()->back();
          }
        } else {
          \Session::put("error","El documento debe ser CSV.");
          return redirect()->back();
        }
      } else {
        \Session::put("error","Debes anexar un documento CSV válido.");
        return redirect()->back();
      }

    }

    public function delgrupo(Request $r){
      \App\dist_grupos::find($r->id)->delete();
      $r->session()->put('status', 'Grupo eliminado');
      return redirect()->back();
    }

    public function switchmat(Request $r){
      $mat = \App\materias::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->deleted_at = $mat->deleted_at == NULL ? \Carbon\carbon::now() : NULL;
      $mat->save();
      return "ok";
    }

    public function switchmateria(Request $r){
      $mat = \App\alumnos_materias::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->estado = $mat->estado == NULL ? 1 : NULL;
      $mat->save();
      return "ok";
    }

    public function bloquematerias(Request $r){
      if(count($r->materias) > 0){
        $materias = \App\alumnos_materias::findMany($r->materias);
        $materias->each(function($i){
          $i->update(["estado"=>1]);
        });
        \Session::put("status","Materias actualizadas");
        return redirect()->back();
      } else {
        \Session::put("error","Debes seleccionar al menos una materia");
        return redirect()->back();
      }
    }

    public function switchdefecto(Request $r){
      \App\estadosdelalumno::all()
        ->each(function($item) use ($r){
          if(md5($item->id) == $r->cid){
            $item->estado = 1;
          } else {
            $item->estado = NULL;
          }
          $item->save();
        });
        return "ok";
    }
    public function switchestados(Request $r){
      $est1 = \App\estadosdelalumno::whereRAW("md5(id)='".$r->cid."'")->first();
      $est1->deleted_at = $est1->deleted_at == NULL ? \Carbon\carbon::now() : NULL;
      $est1->save();
      return $est1;
    }
    public function background(Request $r){
      $est1 = \App\estadosdelalumno::whereRAW("md5(id)='".$r->cid."'")->first();
      $est1->background = $r->val;
      $est1->save();
      return $est1;
    }
    public function color(Request $r){
      $est1 = \App\estadosdelalumno::whereRAW("md5(id)='".$r->cid."'")->first();
      $est1->color = $r->val;
      $est1->save();
      return $est1;
    }
    public function switchperiodos(Request $r){
      $mat = \App\periodos::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->deleted_at = $mat->deleted_at == NULL ? \Carbon\carbon::now() : NULL;
      $mat->save();
      return $mat;
    }

    public function switchtiposdematerias(Request $r){
      $mat = \App\tiposdematerias::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->deleted_at = $mat->deleted_at == NULL ? \Carbon\carbon::now() : NULL;
      $mat->save();
      return "ok";
    }
    public function switchtiposdereprobadas(Request $r){
      $mat = \App\tiposdereprobadas::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->deleted_at = $mat->deleted_at == NULL ? \Carbon\carbon::now() : NULL;
      $mat->save();
      return "ok";
    }
    public function estadodelalumno(Request $r){
      \App\estadosdelalumno::create([
        "name" => $r->name,
        "background" => $r->background,
        "color" => $r->color
      ]);

      $r->session()->put('status', 'Estado del alumno dado de alta');
      return redirect()->back();
    }
    public function materias(Request $r){
      \App\materias::create([
        "name" => $r->name,
        "clave" => $r->clave,
        "seriacion" => $r->seriacion,
        "numero" => $r->numero,
        "creditos" => $r->creditos,
        "rvoe_id" => $r->rvoe_id,
        "planescolar_id" => $r->planescolar_id,
        "tipomateria_id" => $r->tipomateria_id,
      ]);

      $r->session()->put('status', 'Materia creada');
      return redirect()->back();
    }
    public function actualizarmaterias(Request $r){
      $mat = \App\materias::whereRAW("md5(id)='".$r->cid."'")->first();
      $mat->update(
        [
          "name" => $r->name,
          "clave" => $r->clave,
          "seriacion" => $r->seriacion,
          "numero" => $r->numero,
          "creditos" => $r->creditos,
          "rvoe_id" => $r->rvoe_id,
          "planescolar_id" => $r->planescolar_id,
          "tipomateria_id" => $r->tipomateria_id,
        ]
      );
      $r->session()->put('status', 'Materia actualizada');
      return redirect()->back();
    }

    public function periodos(Request $r){
      \App\periodos::create([
        "periodo" => $r->periodo,
        "clave" => $r->clave,
        "fecha_inicio" => $r->fecha_inicio,
        "fecha_termino" => $r->fecha_termino,
        "sede_id" => $r->sede_id
      ]);

      $r->session()->put('status', 'Periodo creado');
      return redirect()->back();
    }

    public function tiposdematerias(Request $r){
      \App\tiposdematerias::create([
        "name" => $r->name
      ]);

      $r->session()->put('status', 'Tipo de materias creado');
      return redirect()->back();
    }

    public function tiposdereprobadas(Request $r){
      \App\tiposdereprobadas::create([
        "name" => $r->name
      ]);

      $r->session()->put('status', 'Tipo de materias reprobadas creado');
      return redirect()->back();
    }

    public function copiarperiodos(Request $r){
      $periodos = \App\periodos::where("sede_id",$r->from_id)->where("deleted_at",NULL)->get();
      $periodosdestino = \App\periodos::select(["clave","periodo"])->where("sede_id",$r->to_id)->get()->toArray();
      $to = $r->to_id;
      $cg = 0;
      $periodos->each(function($i) use($to,$periodosdestino,&$cg){
        $coincidencias = 0;
        foreach ($periodosdestino as $e) {
          if($e["clave"] == $i->clave && $e["periodo"] == $i->periodo){
            $coincidencias++;
          }
        }
        if ($coincidencias == 0) {
          $c = \App\periodos::create([
            "periodo" => $i->periodo,
            "clave" => $i->clave,
            "fecha_inicio" => $i->fecha_inicio,
            "fecha_termino" => $i->fecha_termino,
            "sede_id" => $to
          ]);
        } else {
          $cg++;
        }
      });
      $extra = $cg == 0 ? "" : ", coincidencias evitadas $cg";
      $r->session()->put('status', 'Copia existosa'.$extra);
      return redirect()->back();
    }
}

@php
  if(!(Request::has("_token"))){
    echo "Debes incluir el acceso _token.";
    exit();
  }
  if (!Request::has("set")) {
    echo "Debes incluir el argumento set.";
    exit();
  }
  $sedes = Request::get("set");
  $sede = (\App\sedes::whereRAW("md5(sede)='".$sedes."'")->first());
  if($sede == null){
    echo "El argumento set no es válido.";
    exit();
  }
  $seder = \App\sede_usuario::where("sede_id",$sede->id)->first();
  if (md5($seder->sede->id) != Request::get("_token")) {
    echo "_token no válido.";
    exit();
  }

  if (!Request::has("mat")) {
    echo "Debes incluir el argumento mat.";
    exit();
  }
  $mat = Request::get("mat");
  if(!Request::has("grp")){
    echo "Debes incluir el argumento grp.";
    exit();
  }
  $grp = Request::get("grp");
  if(!Request::has("mto")){
    echo "Debes incluir el argumento mto.";
    exit();
  }
  $mto = Request::get("mto");
  if(!Request::has("int")){
    echo "Debes incluir el argumento int.";
    exit();
  }
  $int = Request::get("int");
  if(!Request::has("tpg")){
    echo "Debes incluir el argumento tpg.";
    exit();
  }
  $tpg = Request::get("tpg");
  if(!Request::has("cto")){
    echo "Debes incluir el argumento cto.";
    exit();
  }
  $cto = Request::get("cto");

  $current_count = $sede->factura_count ?: 0;
  $current_count++;

  $pok = \App\pagos::create(
    [
      "fecha_pago"=>\Carbon\carbon::now(),
      "concepto"=>$cto,
      "interes"=>$int,
      "sede_folio"=>$current_count,
      "sede_id"=>$sede->id,
      "clave"=>$tpg,
      "monto"=>$mto,
      "matricula"=>$mat,
      "document_id" => 0
    ]);

  $status = "500";
  if($pok){
    $sede->factura_count = $current_count;
    $sede->save();
    \App\grupos::where("matricula",$mat)->update(["grupo"=>$grp]);
    $status = 200;
    $pok->grupo = $grp;
    $data["data"] = $pok;
  }


  $data["status"] = $status;
  header("Content-type: json/application");
  echo json_encode($data);
@endphp

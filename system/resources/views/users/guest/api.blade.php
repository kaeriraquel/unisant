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
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  $date = \Carbon\carbon::now()->startOfMonth()->subMonth(3);
  $ac1 = \App\pagos::where("sede_id",$seder->id)->where("created_at",">","$date")->get()->groupBy("matricula");
  $ac = count($ac1);
@endphp
@php
  $total_pagado = 0;
  $i = 0;
  $pasarela = 0;
  $ps = \App\pagos::whereHas("sede",function($q) use ($seder){
    $q->where("sede_id",$seder->sede->id);
  })->where("estado",NULL)->where("deleted_at",NULL)->where("returned_at",NULL)->get();
@endphp
@foreach ($ps as $pago)
  @php
    $i++;
    $total_pagado += $pago->monto;
    if($pago->clave == "Tarjeta")
      $pasarela++;
  @endphp
@endforeach
@php
  $costo = 350;
  $pas = 67;
  $divisa = $sede->div ? $sede->div->divisa : "MXN";
  switch($divisa){
    case "USD":
      $costo = 17.5;
      $pass = 3.2;
    break;
  }
  $total_costo = ($i * $costo);
  $total_pasarelas = ($pasarela * $pas);
  $total = $total_pagado-$total_costo-$total_pasarelas;
  $data["sede"] = $sede->sede;
  $data["value"] = ($total*.3)."";
  $data["pagos"] = ($i)."";
  $data["activos"] = $ac;
  $data["divisa"] = $divisa;
  header("Content-type: json/application");
  echo json_encode($data);
@endphp

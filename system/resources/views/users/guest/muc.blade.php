@php
  $url = ("https://docs.google.com/spreadsheets/d/e/2PACX-1vT_ktwxjtS8ZqPuvEcVaz9_qEKOQt-gSPo5YShi4PxuzbaqWPDfws7PZL2u6tfa3T8Ynangx_Jrbqe-/pub?gid=486333342&single=true&output=csv");
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

  $data = curl_exec($curl);
  curl_close($curl);

  $rows = explode("\n",$data);
  $pagos = [];
  $ins = 0;
  $baja = 0;
  $falt = 0;
  foreach ($rows as $colValue) {
    $col = explode(",",$colValue);
    if(!empty($col[5]) && !strstr($col[4],"Costos")){
      if(!isset($pagos[$col[4]]))
        $pagos[$col[4]] = 0;
      $pagos[$col[4]]++;
      if($col[5] == "SI"){
        $ins++;
      } elseif($col[5] == "BAJA"){
        $baja++;
      } else {
        $falt++;
      }
    }
  }

  $totales = [];
  $total = 0;
  foreach($pagos as $pago => $cuenta){
    $totales["datos"][$pago] = [$pago * 1000 * $cuenta,$cuenta];
    $total += $totales["datos"][$pago][0];
  }
  $totales["total"] = $total;
  $totales["inscritos"] = $ins;
  $totales["bajas"] = $baja;
  $totales["nopago"] = $falt;

  header("Content-type: json/application");
  echo json_encode($totales);
@endphp

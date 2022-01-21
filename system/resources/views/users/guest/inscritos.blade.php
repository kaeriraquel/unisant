@php
  $url = ("https://docs.google.com/spreadsheets/d/e/2PACX-1vR99Y962pxrH3bIR0J8mifzzu0VsTHVNjg5nP02173JY7Zk-fB7XZNE2N5nDc5LN3A7EeoTWtUdmp2C/pub?gid=1278700730&single=true&output=csv");
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

  $data = curl_exec($curl);
  curl_close($curl);

  $inscritos = 0;
  $total_inscritos = 0;
  $total_pasarela = 0;

  $rows = explode("\n",$data);
  $rows_re = array_reverse($rows);
  $corte = false;
  foreach ($rows_re as $colValue) {
    $col = explode(",",$colValue);
    foreach ($col as $colum) {
      if(strstr($colum,"CORTE")){
        $corte = true;
        break;
      }
    }
    if($corte){
      break;
    } else {
      $precio = str_replace("$","",str_replace("\"","",$col[10].$col[11]));
      if(!is_numeric($precio))
        $precio = str_replace("$","",str_replace("\"","",$col[11].$col[12]));
      $inscritos++;
      $total_inscritos += $precio;
      if($col[9] != "Transferencia")
        $total_pasarela++;
    }
  }

  $data2["inscritos"] = $inscritos;
  $data2["total"] = ($total_inscritos - ($total_pasarela * 67) - ($inscritos * 100));

  header("Content-type: json/application");
  echo json_encode($data2);
@endphp

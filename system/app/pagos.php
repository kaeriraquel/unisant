<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagos extends Model
{
    protected $table = "pagos";
    protected $guarded = [];

    public function sede(){
      return $this->hasOne("\App\sede_usuario","id","sede_id");
    }
    public function sedex(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
    public function alumno(){
      return $this->hasOne("\App\alumnosest","matricula","matricula");
    }
    public function pasarela(){
      return $this->hasOne("\App\pasarelas","id","pasarela_id");
    }
    public function grupo(){
      return $this->hasOne("\App\grupos","matricula","matricula");
    }
    public function factura(){
      return $this->hasOne("\App\\facturas","pago_id","id");
    }
    public function conciliacion(){
      return $this->hasOne("\App\\conciliaciones","id","conciliacion_id");
    }
    public function plan_pagos(){
      return $this->hasOne("\App\\planes_pagos","pago_id","id");
    }
}

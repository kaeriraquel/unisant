<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planes_pagos extends Model
{
    protected $table = "planes_pagos";
    protected $guarded = [];

    public function alumno_plan(){
      return $this->hasOne("\App\alumnos_planes","id","alumno_plan_id");
    }
    public function pago(){
      return $this->hasOne("\App\pagos","id","pago_id");
    }
}

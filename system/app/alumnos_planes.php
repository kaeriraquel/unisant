<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class alumnos_planes extends Model
{
    protected $table = "alumnos_planes";
    protected $guarded = [];

    public function plan(){
      return $this->hasOne("\App\planespago","id","plan_id");
    }
    public function alumno(){
      return $this->hasOne("\App\alumnosest","matricula","matricula");
    }
    public function nombres(){
      return $this->hasOne("\App\\nombres","matricula","matricula");
    }
    public function planes_pagos(){
      return $this->hasMany("\App\planes_pagos","alumno_plan_id","id");
    }
}

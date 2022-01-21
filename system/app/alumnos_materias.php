<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class alumnos_materias extends Model
{
    protected $table = "alumnos_materias";
    protected $guarded = [];

    public function reprobada(){
      return $this->hasOne("\App\\tiposdereprobadas","id","reprobada_id");
    }
    public function alumno(){
      return $this->hasOne("\App\alumnosest","id","alumno_id");
    }
    public function materia(){
      return $this->hasOne("\App\materias","id","materia_id");
    }
    public function periodo(){
      return $this->hasOne("\App\periodos","id","periodo_id");
    }
}

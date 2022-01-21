<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class materiasimportadas extends Model
{
    protected $table = "materias_importadas";
    protected $guarded = [];

    public function alumno(){
      return $this->hasOne("\App\alumnosest","matricula","matricula");
    }

    public function materia(){
      return $this->hasOne("\App\materias","id","materia_id");
    }

    public function periodo(){
      return $this->hasOne("\App\periodos","id","periodo_id");
    }

    public function tiporeprobada(){
      return $this->hasOne("\App\\tiposdereprobadas","id","tiporeprobada_id");
    }

}

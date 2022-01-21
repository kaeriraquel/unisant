<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nombres extends Model
{
    protected $table = "nombres";
    protected $guarded = [];

    public function alumno(){
      return $this->hasOne("\App\alumnosest","matricula","matricula");
    }
    public function planespago(){
      return $this->hasMany("\App\\alumnos_planes","matricula","matricula");
    }
    public function grupo(){
      return $this->hasOne("\App\grupos","matricula","matricula");
    }
    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
}

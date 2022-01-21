<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class conciliaciones extends Model
{
    protected $table = "conciliaciones";
    protected $guarded = [];

    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }

    public function pagos(){
      return $this->hasMany("\App\\pagos","conciliacion_id","id");
    }
    public function terms(){
      return $this->hasMany("\App\\terminologia_fecha","conciliacion_id","id");
    }
    public function requerimientos(){
      return $this->hasMany("\App\\requerimientos","conciliacion_id","id");
    }
    public function conceptos(){
      return $this->hasMany("\App\\conciliaciones_conceptos","conciliacion_id","id");
    }
    public function opciones(){
      return $this->hasMany("\App\\conciliacion_opciones","conciliacion_id","id");
    }
}

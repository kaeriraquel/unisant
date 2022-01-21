<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class alumnosest extends Model
{
    protected $table = "alumnosest";
    protected $guarded = [];

    public function grupo(){
      return $this->hasOne("\App\grupos","matricula","matricula");
    }
    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
    public function facturacion(){
      return $this->hasOne("\App\\facturacion","alumno_id","id");
    }
    public function facturacionFolio(){
      return $this->hasOne("\App\\facturacion","folio_id","id");
    }
    public function datosacademicos(){
      return $this->hasOne("\App\\antecedentes","alumno_id","id");
    }
    public function iadicional(){
      return $this->hasOne("\App\\iadicional","alumno_id","id");
    }
    public function revoe(){
      return $this->hasOne("\App\\revoes","id","revoe_id");
    }
    public function materias(){
      return $this->hasMany("\App\\alumnos_materias","alumno_id","id");
    }
    public function planespago(){
      return $this->hasMany("\App\\alumnos_planes","matricula","matricula");
    }
}

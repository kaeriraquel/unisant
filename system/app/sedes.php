<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sedes extends Model
{
    protected $table = "sedes";
    protected $guarded = [];

    public function sedex(){
      return $this->hasOne("\App\sede_usuario","sede_id","id");
    }
    public function div(){
      return $this->hasOne("\App\divisas","id","divisa_id");
    }

    public function revoes(){
      return $this->hasMany("\App\\revoes_sedes","sede_id","id");
    }
    public function alumnos(){
      return $this->hasMany("\App\\alumnosest","sede_id","id");
    }
    public function conciliaciones(){
      return $this->hasMany("\App\\conciliaciones","sede_id","id");
    }
    public function pagos_pendientes(){
      return $this->hasMany("\App\\pagos","sede_id","id")->where("estado",null);
    }
    public function planespago(){
      return $this->hasMany("\App\\planes_sedes","sede_id","id");
    }
    public function conceptos(){
      return $this->hasMany("\App\\conceptos_sedes","sede_id","id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sede_usuario extends Model
{
    protected $table = "sede_usuario";
    protected $guarded = [];

    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
    public function usuario(){
      return $this->hasOne("\App\User","id","usuario_id");
    }

    public function pagos(){
      return $this->hasMany("\App\pagos","sede_id","id");
    }

    public function pagos_pendientes(){
      return $this->hasMany("\App\\pagos","sede_id","id")->where("estado",null);
    }
}

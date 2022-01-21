<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class accesos_sedes extends Model
{
    protected $table = "accesos_sedes";
    protected $guarded = [];

    public function owner(){
      return $this->hasOne("\App\User","id","owner_id");
    }
    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
}

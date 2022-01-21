<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class revoes_sedes extends Model
{
    protected $table = "revoes_sedes";
    protected $guarded = [];

    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }

    public function revoe(){
      return $this->hasOne("\App\\revoes","id","revoe_id");
    }
}

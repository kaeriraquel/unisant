<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class periodos extends Model
{
    protected $table = "periodos";
    protected $guarded = [];
    
    public function sede(){
      return $this->hasOne("\App\sedes","id","sede_id");
    }
}

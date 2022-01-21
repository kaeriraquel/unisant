<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class conciliacion_opciones extends Model
{
    protected $table = "conciliacion_opciones";
    protected $guarded = [];

    public function concepto(){
      return $this->hasOne("\App\conceptosdist","id","concepto_id");
    }
}

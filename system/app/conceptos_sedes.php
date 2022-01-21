<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class conceptos_sedes extends Model
{
    protected $table = "conceptos_sedes";
    protected $guarded = [];

    public function concepto(){
      return $this->hasOne("\App\conceptospago","id","concepto_id");
    }
}

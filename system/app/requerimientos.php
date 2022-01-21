<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class requerimientos extends Model
{
    protected $table = "requerimientos";
    protected $guarded = [];

    public function con(){
      return $this->hasOne("\App\conceptos","id","concepto");
    }
}

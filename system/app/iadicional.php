<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class iadicional extends Model
{
    protected $table = "iadicional";
    protected $guarded = [];

    public function pago(){
      return $this->hasOne("\App\\alumnosest","id","alumno_id");
    }
}

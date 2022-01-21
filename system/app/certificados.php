<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class certificados extends Model
{
    protected $table = "certificados";
    protected $guarded = [];

    public function grado(){
      return $this->hasOne("\App\\titulos","matricula","matricula");
    }

}

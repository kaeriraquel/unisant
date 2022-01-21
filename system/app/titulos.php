<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class titulos extends Model
{
    protected $table = "titulos";
    protected $guarded = [];

    public function acta(){
      return $this->hasOne("\App\\actas","matricula","matricula");
    }

    public function certificado(){
      return $this->hasOne("\App\\certificados","matricula","matricula");
    }
}

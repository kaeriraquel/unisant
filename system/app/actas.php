<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actas extends Model
{
    protected $table = "actas";
    protected $guarded = [];

    public function certificado(){
      return $this->hasOne("\App\certificados","matricula","matricula");
    }
    public function grado(){
      return $this->hasOne("\App\\titulos","matricula","matricula");
    }
}

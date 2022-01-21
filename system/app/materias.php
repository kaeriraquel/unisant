<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class materias extends Model
{
    protected $table = "materias";
    protected $guarded = [];

    public function planescolar(){
      return $this->hasOne("\App\planescolar","id","planescolar_id");
    }
    public function tipodemateria(){
      return $this->hasOne("\App\\tiposdematerias","id","tipomateria_id");
    }

}

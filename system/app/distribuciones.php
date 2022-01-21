<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class distribuciones extends Model
{
    protected $table = "distribuciones";
    protected $guarded = [];

    public function conceptos(){
      return $this->hasMany("\App\conceptosdist","distribucion_id","id")->orderby("tipo","asc");
    }

    public function dist_grupos(){
      return $this->hasMany("\App\dist_grupos","dist_id","id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class revoes extends Model
{
    protected $table = "revoes";
    protected $guarded = [];

    public function materias(){
      return $this->hasMany("\App\materias","rvoe_id","id")->where("deleted_at",NULL);
    }
    public function materiasarchivadas(){
      return $this->hasMany("\App\materias","rvoe_id","id")->where("deleted_at","<>",NULL);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planes_sedes extends Model
{
    protected $table = "planes_sedes";
    protected $guarded = [];

    public function plan(){
      return $this->hasOne("\App\planespago","id","plan_id");
    }
}

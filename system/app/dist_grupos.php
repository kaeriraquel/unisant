<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dist_grupos extends Model
{
    protected $table = "dist_grupos";
    protected $guarded = [];

    public function dist(){
      return $this->hasOne("\App\distribuciones","id","dist_id");
    }
}

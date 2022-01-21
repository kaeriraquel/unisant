<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class grupos extends Model
{
    protected $table = "grupos";
    protected $guarded = [];

    public function dist_grupos(){
      return $this->hasOne("\App\dist_grupos","grupo","grupo");
    }
}

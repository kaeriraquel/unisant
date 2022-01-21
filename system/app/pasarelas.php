<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pasarelas extends Model
{
    protected $table = "pasarelas";
    protected $guarded = [];

    public function con(){
      return $this->hasOne("\App\conceptospago","id","concepto_id");
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class planespago extends Model
{
    protected $table = "planespago";
    protected $guarded = [];

    public function conceptopago(){
      return $this->hasOne("\App\conceptospago","id","concepto_id");
    }
}

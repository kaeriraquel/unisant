<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class conceptosdist extends Model
{
    protected $table = "conceptosdist";
    protected $guarded = [];

    public function concepto_pago(){
      return $this->hasOne("\App\conceptospago","id","conceptopago");
    }
}

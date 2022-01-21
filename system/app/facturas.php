<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class facturas extends Model
{
    protected $table = "facturas";
    protected $guarded = [];

    public function pago(){
      return $this->hasOne("\App\\pagos","id","pago_id");
    }
}

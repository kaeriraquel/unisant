<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'nivel_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sede(){
      return $this->hasOne("\App\sede_usuario","usuario_id","id");
    }
    public function accesos(){
      return $this->hasMany("\App\accesos_sedes","owner_id","id");
    }
    public function nivel(){
      return $this->hasOne("\App\\nivel","id","nivel_id");
    }
    public function montos(){
      return $this->hasOne("\App\\montos","id","usuario_id");
    }
}

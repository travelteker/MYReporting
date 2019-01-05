<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'users';
    protected $primaryKey = 'id_user'; //Personalizar el nombre de la clave primaria --> default 'id'

    protected $fillable = [
        'name',
        'email',
        'password'
    ];


    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
    

}
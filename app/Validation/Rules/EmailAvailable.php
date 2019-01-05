<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule
{
    public function validate($input)
    {
        //Este método debe devolver TRUE o FALSE para la validación.
        //Chequear BD tabla USERS para comprobar que no esté ya registrado el email que se quiere insertar
        return User::where('email', $input)->count() === 0;
    }
}
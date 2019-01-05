<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class MatchesPassword extends AbstractRule
{
    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }


    public function validate($input)
    {
        //Este método debe devolver TRUE o FALSE para la validación.
        return password_verify($input, $this->password);
    }
}
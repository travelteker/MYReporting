<?php

namespace App\Auth;

use App\Models\User;


class Auth
{

    public function check()
    {
        //Comprobar si existe session de usaurio y no es null. Devolverá booleano
        return isset($_SESSION['user']);
    }


    public function user()
    {
        $userRegistered = null;
        //Devolver los datos del usuario que tiene session activa
        if($this->check())
        {
            $userRegistered = User::find($_SESSION['user']);
        }
        return $userRegistered;
    }


    public function attempt($email, $password)
    {
        //grab the user by email
        $user = User::where('email', $email)->first();

        if(!$user){
            return false;
        }
        
        //verify password for that user
        //set into session
        if(password_verify($password, $user->password))
        {
            $_SESSION['user'] = $user->id_user;
            return true;
        }

        return false;

    }


    public function logout()
    {
        unset($_SESSION{'user'});
    }
}
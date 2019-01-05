<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller
{
    public function getChangePassword($request, $response)
    {
        return $this->view->render($response, 'auth/password/change.twig');
    }

    public function postChangePassword($request, $response)
    {
        //Personalizamos la regla de validacion 'matchesPassword' para comprobar que la que introduce es la que está grabada en el sistema
        $validation = $this->validator->validate($request, [
            'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->user()->password),
            'password_new' =>v::noWhitespace()->notEmpty(),
        ]);

        if($validation->failed())
        {
            return $response->withRedirect($this->router->pathFor('auth.password.change'));
        }

        $this->auth->user()->setPassword($request->getParam('password_new'));

        $this->flash->addMessage('info', 'Se ha actualizado su password correctamente!');
        return $response->withRedirect($this->router->pathFor('home'));
    }


}
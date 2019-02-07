<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{

    public function getSignOut($request, $response)
    {
        //sign out
        $this->auth->logout();
        //redirect
        return $response->withRedirect($this->router->pathFor('home'));
    }


    /**
     * Renderizar la vista del LOGIN USER
     * @param [ojbect] $request
     * @param [object] $response
     * @return [string] 
     */
    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'auth/signin.twig');
    }


    /**
     * Envio de los datos del formulario LOGIN para acceder como usuario válido
     *
     * @param object $request
     * @param object $response
     * @return string
     */
    public function postSignIn($request, $response)
    {
        //Validar CSRF
        if (false === $request->getAttribute('csrf_status')) {
            $this->flash->addMessage('error', 'Error, no se pudo realizar la petición al servidor. Vuelva a intentarlo y si el error continua,
             por favor contacte con soporte e indique el siguiente codigo de error, "Code:1aA00x_001"');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        //Validar email y password del usario en la solicitud de login
        //La comprobación devolverá siempre un booleano
        $auth = $this->auth->attempt($request->getParam('email'), $request->getParam('password'));
        if(!$auth)
        {
            $this->flash->addMessage('error', 'No se puede realizar el LOGIN con los datos facilitados!');
            //Redireccionamos al login nuevamente
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        //Redireccionamos a pagina reporting del usuario cuando se loguea correctamente
        return $response->withRedirect($this->router->pathFor('reports'));
    }


    /**
     * Renderizar la vista del REGISTRO USUARIO
     * @param ojbect $request
     * @param object $response
     * @return string 
     */
    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'auth/signup.twig');
    }


    /**
     * Envio de los datos del formulario ALTA USUARIO en la plataforma
     *
     * @param object $request
     * @param object $response
     * @return string
     */
    public function postSignUp($request, $response)
    {
        //Validar CSRF
        if (false === $request->getAttribute('csrf_status')) {
            $this->flash->addMessage('error', 'Error, no se pudo realizar la petición al servidor. Vuelva a intentarlo y si el error continua,
             por favor contacte con soporte e indique el siguiente codigo de error, "Code:1aA00x_002"');
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::noWhitespace()->notEmpty()
        ]);

        if($validation->failed())
        {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }


        $user = User::create([
            'email' => $request->getParam('email'),
            'name' => $request->getParam('name'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT, ['cost' => 10]),
        ]);

        $this->flash->addMessage('info', 'Has sido registrado satisfactoriamente en la plataforma!');

        //Si el registro se realiza correctamente, entonces creamos una session para dicho user
        $this->auth->attempt($user->email, $request->getParam('password'));

        //Con '$this->router' accedemos al 'container' (contenedor dependencias) y podemos usar el helper 'pathFor()'
        //return $response->withRedirect($this->router->pathFor('home'));
        //Redireccionamos a la página de reports disponibles para el usuario, cuando se registre correctamente
        return $response->withRedirect($this->router->pathFor('reports'));
    }
}
<?php

namespace App\Middleware;

class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        //Una vez logueado el usuario protegemos las rutas, 'signin' y 'singup' forzando la redirección al 'home'
        if($this->container->auth->check())
        {
            return $response->withRedirect($this->container->router->pathFor('home'));
        }

        $response = $next($request, $response);
        return $response;
    }
}
<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        //Chequear si el usuario no está logueado en el sistema
        if(!$this->container->auth->check())
        {
            $this->container->flash->addMessage('error', 'Por favor, identifiquese como usuario válido del sistema.');
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        $response = $next($request, $response);
        return $response;
    }
}
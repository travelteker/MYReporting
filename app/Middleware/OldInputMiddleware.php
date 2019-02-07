<?php

namespace App\Middleware;


class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        //Para tener de forma permanente los datos rellenados en el form register user
        //Así cuando se refresque la pagina y se haga la validación tendremos persistencia de datos.

        if(isset($_SESSION['old']))
        {
            $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
            //Poner la SESSION despues del conatiner
            $_SESSION['old'] = $request->getParams();
        }
       

        $response = $next($request, $response);
        return $response;
    }
}
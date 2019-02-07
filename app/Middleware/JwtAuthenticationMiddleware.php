<?php

namespace App\Middleware;


class JwtAuthenticationMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {

        $data["status"] = "error";
        $data["message"] = "Token no encontrado";
        $response->withHeader("Content-Type", "application/json")
                 ->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        
        $response = $next($request, $response);
        return $response;
    }


    
}
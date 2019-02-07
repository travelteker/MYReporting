<?php

use App\Middleware\ReportingMiddleware;
use App\Middleware\JwtAuthenticationMiddleware;

$app->group('/api', function() use ($container){

    //code --> parametro para identificar a los usuarios con acceso a la petición de token
    //El código deberá estar asociado a la IP del cliente, para identificar a los usuarios de esa IP.
    $this->post('/token/{code}', 'ApiController:getToken');

    $this->group('/v1', function() use ($container){
        // http://127.0.0.1/slimCurso/public/api/v1/inventario_productos/excel
        $this->post('/{metodo}/{formato}','ReportsController:prepareFile')->add(new ReportingMiddleware($container));
    });
});

/*
Recordar que las peticiones API, necesitan 2 cabeceras:
    - Myrtkn --> random value      No se válida, es para distinguir las peticiones que van por API de las que son VIA PORTAL WEB. (Valor random, no se valida)
    - X-Token --> Nombre de cabecera elegido, para buscar el TOKEN de acceso
                  El token de acceso está formando por un string con 2 partes:
                  1. La palabra 'Bearer'
                  2. Un espacion en blanco
                  3. Y seguidamente el código JWT generado
                  Ejemplo:
                        Key -> X-Token 
                        Value -> Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDc0MTIxNzEsImV4cCI6MTU0NzQxNTc3MSwianRpIjoiUXBUTkNEVUR5alJGOWQ4UlFialFBVUFDZ2c2aXk3TXZJaVJFQlloUzlTMGQxbmJuM1VJbTRIV3Z1UEg4aGlKRFhFVXpOeUo0UmFJMkw5NHFNMTUxZUlwZVFRU0xPeUV2c2tJbHlZZFVLaTJOaTlBTnpBTFlFWUYyUVlKWGk2cnBhMUVJZFdtZlFsczhsWlN3UGlmcm41OHdWbWtnMjk2aXdkZUVSUWVHdTBwbiIsInN1YiI6eyJjbGllbnQiOiIxMjcuMC4wLjEiLCJjb2RlIjoiY29kaWdvIiwiYXR0cmlidXRlIjoiand0In19.Nz-2BQVg2NqJNG4upbCOMDF1JhKWSzT1LvM0lK6_cW0
*/




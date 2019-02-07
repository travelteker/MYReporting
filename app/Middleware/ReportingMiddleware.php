<?php

namespace App\Middleware;

class ReportingMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        //La 3º posicion del atributo routeInfo contiene los parametros de la request
        $args = $request->getAttribute('routeInfo')[2];

        //Método de clase que tenemos que ejecutar para obtener los datos que vamos a enviar a fichero
        $metodo = (isset($args['metodo'])) ? $args['metodo'] : null;
        //Formato de salida del fichero
        $formato = (isset($args['formato'])) ? strtolower($args['formato']) : null;

        $method = $request->getMethod();

        //Si no se pasan los parametros de la ruta SLIM devolverá un error 404, por no coincidir con ninguna de las rutas declaradas en el sistema.

        if(!$this->formatosPermtidos($formato))
        {
            if(strtoupper($method) == 'GET')
            {
                $this->container->flash->addMessage('error', 'El formato solicitado no es un formato valido, contacte con soporte.');
                return $response->withRedirect($this->container->router->pathFor('reports'));
            }
            else
            {
                $data = ['status'=>'KO', 'description' => 'Error en la petición, por favor contacte con soporte@soporte.soporte', 'code' => 'A001x'];
                return $response->withJson($data);
            }
            
        }

        $response = $next($request, $response);
        return $response;
    }


     /**
     * Validar los formatos de ficheros válidos para descargar
     *
     * @param string $formato
     * @return bool
     */
    private function formatosPermtidos($formato)
    {
        $formatosValidos = [
            'excel' => 0,
            'pdf' => 1
        ];

        $valido = false;
        if(isset($formatosValidos[$formato]))
        {
            $valido = true;
        }
        return $valido;
    }
}
<?php

namespace App\Controllers;


use App\Models\User;
use Slim\Views\Twig as View;

use App\Helpers\AuxFunctions as Aux;


use Slim\Http\Stream;


class ReportsController extends Controller
{
    
    public function index($request, $response)
    {
        $id_user = $this->auth->user()->id_user;

        $params = array();
        //Buscar los reports que tenga asociado el usuario a través de us 'id_user'
        $reporting = $this->ReportsUser->reportsByUser($id_user);
        if(!empty($reporting))
        {
            $params['disponibles'] = $reporting;
        }

        return $this->view->render($response, 'reports.twig', $params);
    }


    /**
     * Recibir los parámetros desde la URI de llamada e invocar el método de preparación de fichero correspondiente
     * Con este metodo podemos atender distintas rutas de forma dinamica usando solo un metodo en el route.
     *
     * @param object $request
     * @param object $response
     * @param array $args
     * @return void
     */
    public function prepareFile($request, $response, $args)
    {
        
        $metodo = $args['metodo'];
        $formato = $args['formato'];

        //Con el middleware 'ReportingMiddleware.php' nos aseguramos que el formato que llega es el correcto. Sino se manda flash-message avisando del error.

        //Convención:
        //Los nombres de los metodos de clase se declaran en la request, en minúscula, y si son varias palabras, se separarán por el carácter '_'
        $metodo = Aux::parsingMethodName($metodo);
        $extFile = Aux::mappingExtensionsFile($formato);

        //Invocar los metodos de forma dinamica --> array_call_function PHP

        //Para tener disponibles los parametros $request, $response y $args en cada metodo que invoquemos dinamicamente
        //Devuelve el valor devuelto por la llamada de retorno o FALSE en caso de ERROR
        //Los métodos invocados preparan los ficheros o los datos que queramos devolver
        call_user_func_array(array($this, $metodo), array());

        $nameFile = $metodo.$extFile;
        return $response->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withHeader('Content-Disposition', 'attachment; filename="' .$nameFile. '"')
                        ->withHeader('Expires', '0')
                        ->withHeader('Cache-Control', 'must-revalidate')
                        ->withHeader('Pragma', 'public');
                        

        //Guardamos el fichero creado en excel en un directorio y con un cron diario se vacia el contenido del directorio; servirá a modo de caché --> EVALUAR
        //Puede que haya consultas MySQL que sean lentas por la enorme cantidad de registros a procesar, y podría ser interesante cachear, los informes se supone que se
        //piden para un rango de fechas concreto o un rango de hora concreto, NO ES UNA APLICACION PARA TIEMPO REAL.

    }


    private function inventarioProductos()
    {
        $file = __DIR__.'/../../public/water.png';
        readfile($file);
    }



}
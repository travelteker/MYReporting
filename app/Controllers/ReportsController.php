<?php

namespace App\Controllers;


use App\Models\User;
use Slim\Views\Twig as View;

use App\Helpers\AuxFunctions as Aux;


use Slim\Http\Stream;


class ReportsController extends Controller
{
    //TODO >>> Si guardamos el fichero creado en el directorio del servidor, crear un cron diario para vaciar el contenido del directorio.
    //Servirá a modo de caché >>> Para el mismo día si existe el fichero servir el ya creado y no lanzar consulta contra la BD.

    const SAVE_IN_SERVER = false;   //Si queremos guardar los ficheros en el directorio 'Downloads'
    

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

        //Con 'ReportingMiddleware.php' nos aseguramos que el formato que llega es el correcto. Sino se manda flash-message avisando del error.

        //Convención:
        //Los nombres de los metodos de clase se declaran en la request, en minúscula, y si son varias palabras, se separarán por el carácter '_'
        $metodo = Aux::parsingMethodName($metodo);
        $extFile = Aux::mappingExtensionsFile($formato);

        //Invocar los metodos de forma dinamica --> array_call_function PHP

        //Para tener disponibles los parametros $request, $response y $args en cada metodo que invoquemos dinamicamente
        //Devuelve el valor devuelto por la llamada de retorno o FALSE en caso de ERROR
        //Los métodos invocados preparan los ficheros o los datos que queramos devolver
        if(method_exists($this, $metodo))
        {
            $params = ['formato' => $formato, 'extension' => $extFile];
            $pathAbsoluteFile = call_user_func_array(array($this, $metodo), array($params));
            $nameFile = Aux::showNameInDownload($pathAbsoluteFile);
            $this->modo($pathAbsoluteFile); 
            return $this->download($response, $nameFile);
        }
        else
        {
            $this->flash->addMessage('error', 'No se han podido volcar los datos a fichero. Contacte con soporte.');
            //Redireccionamos al login nuevamente
            return $response->withRedirect($this->router->pathFor('reports'));
        }                

    }


    /**
     *Método para obtener los datos y volcarlos al tipo de informe solicitado
     *
     * @param [type] $params
     * @return string
     */
    private function inventarioProductos($params)
    {
        $ext = $params['extension'];
        $pathExcelFileName = $this->configureAbsolutePathFile(__FUNCTION__, $ext);

        //Las clases fueron configuradas para estar disponibles desde el contenedor de dependencias de slim.
        //Creamos un nuevo libro excel --> 'hoja de cálculo = spreadsheet'
        $spreadsheet = $this->spreadsheet;

        //Configurar los metadatos para el fichero excel
        $this->configureMetaData($spreadsheet, __FUNCTION__);

        // Configurar hoja como activa, por defecto la primera hoja es con indice 0.
        $sheet = $spreadsheet->getActiveSheet();
        // Personalizar el nombre de la hoja activa
        $sheet->setTitle(__FUNCTION__);
        //Añadir valores a las celdas
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->setCellValue('A2', 'Goodbye World !');

        return $pathExcelFileName;

    }


    /**
     * Configurar el path absoluto donde se va a guardar el fichero que generamos para descarga
     *
     * @param string $nameMethod
     * @param string $ext
     * @return string
     */
    private function configureAbsolutePathFile($nameMethod, $ext)
    {
        $rootFile = __DIR__ . DS . '..' . DS . '..' . DIRECTORY_SEPARATOR . DIR_EXCELS . DS;
        $nameFile = date('Ymd').'_'.$nameMethod.$ext;
        return $rootFile.$nameFile;
    }


    /**
     * Configurar los metadatos del fichero excel
     *
     * @param objeto $spreadsheet
     * @param string $metodo
     * @return void
     */
    private function configureMetaData($spreadsheet, $metodo)
    {
        $spreadsheet->getProperties()
                    ->setCreator("MYReporting")
                    ->setLastModifiedBy("MYReporting")
                    ->setTitle("Office XLSX")
                    ->setSubject("Office XLSX")
                    ->setDescription($metodo)
                    ->setKeywords("excel slim php")
                    ->setCategory("Informe resultados");
    }


    /**
     * Metodo para devolver las cabeceras de la respuesta de la petición realizada
     *
     * @param objeto $response
     * @param string $nameFile
     * @return void
     */
    private function download($response, $nameFile)
    {
        return $response->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withHeader('Content-Disposition', 'attachment; filename="' .$nameFile. '"')
                        ->withHeader('Expires', 'Fri, 11 Nov 2011 11:11:11 GMT')                                  //Never cache
                        ->withHeader('Cache-Control', 'max-age=0, private, no-store, no-cache, must-revalidate')  //Never cache
                        ->withHeader('Pragma', 'public');
    }


    /**
     * Método para especificar el tipo de guardado del fichero generado
     *
     * @param string $pathAbsoluteFile
     * @return void
     */
    private function modo($pathAbsoluteFile)
    {
        //Salida de los datos
        $excelWriter = $this->writerXlsx;
        if(self::SAVE_IN_SERVER === false)
        {
            //Forzar la descargar desde el buffer de salida sin guardar en el servidor
            $excelWriter->save('php://output');
        }
        else
        {
            //Guardar el fichero correspondiente en el PATH especificado
            $excelWriter->save($pathAbsoluteFile);
            //Leer fichero y escribirlo al búfer de salida.
            readfile($pathAbsoluteFile);
        }
    }



}
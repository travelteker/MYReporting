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
    
    protected $extFile = null;  //Para identificar cabecera ContentType de la respuesta en función del tipo fichero a descargar
    protected $pathFile;        //Path nombre fichero que queremos descargar, contiene el nombre personalizado

    /**
     * Método para renderizar pagina html
     *
     * @param objeto $request
     * @param objeto $response
     * @return string
     */
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
        $isApi = Aux::isRequestApi($request);
        
        if($isApi === true)
        {
            //Obtener el TOKEN JsonWebToken (JWT) y validarlo
            $jwt = Aux::getTokenRequest();
        }

        $metodo = $args['metodo'];
        $formato = $args['formato'];
    
        //Con 'ReportingMiddleware.php' nos aseguramos que el formato que llega es el correcto. Sino se manda flash-message avisando del error.

        //Convención:
        //Los nombres de los metodos de clase se declaran en la request, en minúscula, y si son varias palabras, se separarán por el carácter '_'
        $metodo = Aux::parsingMethodName($metodo);
        $extFile = Aux::mappingExtensionsFile($formato);

        $this->extFile = $extFile;

        //Para tener disponibles los parametros $request, $response y $args en cada metodo que invoquemos dinamicamente
        //Devuelve el valor devuelto por la llamada de retorno o FALSE en caso de ERROR
        //Los métodos invocados preparan los ficheros o los datos que queramos devolver
        if(method_exists($this, $metodo))
        {
            $params = ['formato' => $formato, 'extension' => $extFile];
            call_user_func_array(array($this, $metodo), array($params));
            $pathAbsoluteFile = $this->getPathFile();
            $nameFile = Aux::showNameInDownload($pathAbsoluteFile); 
            //$this->document -----> se obtiene del DI del Slim3 ------> ver fichero 'dependencias.php'
            $this->document->saveFile($pathAbsoluteFile, $this->writerXlsx, self::SAVE_IN_SERVER);
            return $this->document->download($response, $nameFile);
        }
        else
        {
            $this->flash->addMessage('error', 'No se han podido volcar los datos a fichero. Contacte con soporte.');
            //Redireccionamos al login nuevamente
            return $response->withRedirect($this->router->pathFor('reports'));
        }                

    }


    /**
     * Obtener el path abosulto del fichero que queremos descargar, en la ubicación pre-configurada
     *
     * @return string
     */
    private function getPathFile()
    {
        return $this->pathFile;
    }


    /**
     *Método para obtener los datos y volcarlos al tipo de informe solicitado
     *
     * @param array $params
     * @return string
     */
    private function inventarioProductos($params)
    {
        $ext = $params['extension'];
        $this->pathFile = Aux::PathDownloadFiles(__FUNCTION__, $ext);

        //Las clases fueron configuradas para estar disponibles desde el contenedor de dependencias de slim.
        //Creamos un nuevo libro excel --> 'hoja de cálculo = spreadsheet'
        $spreadsheet = $this->spreadsheet;

        //Configurar los metadatos para el fichero excel
        //$this->configureMetaData($spreadsheet, __FUNCTION__);
        $owner = null;
        if(isset($_SESSION['name']))
        {
            $owner = $_SESSION['name'];
        }
        $this->document->configMetaDataDocument($spreadsheet, __FUNCTION__, ['owner' => $owner]);

        // Configurar hoja como activa, por defecto la primera hoja es con indice 0.
        $sheet = $spreadsheet->getActiveSheet();
        // Personalizar el nombre de la hoja activa
        $sheet->setTitle(__FUNCTION__);

        //Estilo cabecera
        $spreadsheet->getActiveSheet()->getStyle('A4:C4')->applyFromArray($this->document->styleHeader());

        //Cabecera Report
        $cabecera = [
            'A' => 'Producto', 
            'B' => 'Total', 
            'C' => 'Area'
        ];

        $startRow = 4;
        $starCol = 'A';

        //Queremos 3 columnas sólo
        for($i = 'A'; $i < 'D'; $i++)
        {
            $sheet->setCellValue($i.$startRow, $cabecera[$i]);
            //Auto-size column
            $sheet->getColumnDimension($i)->setAutosize(true);
        }

        //Obtener los datos mediante consulta a la BD a través del Modelo inyectado por DI
        $listadoDatos = $this->Inventory->productosInventario();

                                //array datos a insertar, rellenar con valor NULL, coordenada comienzo
        $sheet->fromArray($this->arrayFilasExcel($listadoDatos), NULL, 'A5');

        

    }


    /**
     * Preparar las filas a escribir en el Excel en función de la cabecera necesaria.
     *
     * @param array $listadoDatos
     * @return array
     */
    private function arrayFilasExcel($listadoDatos)
    {
        $filasExcel = [];
        foreach($listadoDatos as $cod_inventario => $data)
        {
            foreach($data as $area => $arrayArea)
            {
                foreach($arrayArea as $info)
                {
                    $filasExcel[] = [$info['producto'], $info['total'], $area];
                }
            }
        }
        return $filasExcel;
    }

}
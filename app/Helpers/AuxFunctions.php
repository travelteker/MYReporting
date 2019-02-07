<?php

namespace App\Helpers;

class AuxFunctions
{
    public static function parsingMethodName($methodName)
    {
        $partes = explode('_', $methodName);
        $nameFinal = $methodName;
        if(count($partes) > 1){
            foreach($partes as $key => $value)
            {
                if($key == 0)
                {
                    $nameFinal = $value;
                }
                else
                {
                    $nameFinal .= ucwords($value);
                }
            }
        }

        return $nameFinal;
    }


    public static function mappingExtensionsFile($formato)
    {
        $extensions = [
            'excel' => '.xlsx',
            'pdf' => '.pdf' 
        ];

        //Por defecto extensión fichero EXCEL
        $ext = '.xlsx';
        if(isset($extensions[$formato]))
        {
            $ext = $extensions[$formato];
        }
        return $ext;
    }


    /**
     * Devolver la extensión a partir del nombre de fichero formateado como NAME.EXT
     *
     * @param string $nameFile
     * @return string
     */
    public function getExtensionFile($nameFile)
    {
        $extFile = '';     //Valor por defecto
        $partes = explode('.', $nameFile);
        if(count($partes) > 1)
        {
            //La extensión por definición es la última sección del nombre del fichero
            $extFile = end($partes);
        }
        return $extFile;
    }


    /**
     * Obtener el ultimo segemento del nombre del fichero si posee como caracter separador de palabra el char '_'
     *
     * @param string $pathAbsoluteFile
     * @return string
     */
    public static function showNameInDownload($pathAbsoluteFile)
    {
        //Nos quedamos con la ultima parte del path absoluto; corresponde al nombre asignado al fichero
        $nombreFichero = basename($pathAbsoluteFile);
        $aux = $pathAbsoluteFile;
        if(substr_count($nombreFichero, '_') > 0)
        {
            //Nos quedamos con la ultima parte del nombre
            $partes = explode('_', $pathAbsoluteFile);
            $aux = end($partes);
        }
        return $aux;
    }


    /**
     * Configurar el path absoluto donde se va a guardar el fichero que generamos para descarga
     *
     * @param string $nameMethod
     * @param string $ext
     * @return string
     */
    public function PathDownloadFiles($nameMethod, $ext)
    {
        $nameFile = date('Ymd').'_'.$nameMethod.$ext;
        return DIR_DOWNLOADS.$nameFile;
    }


    /**
     * Determinar si la petición es via web o via API
     *
     * @param object $request
     * @return boolean
     */
    public function isRequestApi($request)
    {
        $isApi = false;

        $uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
        $fullUrl = (string) $uri;

        $partes = explode('/',$fullUrl);
        $invertirPartes = array_flip($partes);
        if(isset($invertirPartes['api']))
        {
            $isApi = true;
        }
        return $isApi;
    }


    /**
     * Obtener el JWT de la petición entrante
     *
     * @return void
     */
    public function getTokenRequest()
    {
        $jwt = null;
        return $jwt;
    }

}
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
}
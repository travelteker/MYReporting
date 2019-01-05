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

        $ext = null;
        if(isset($extensions[$formato]))
        {
            $ext = $extensions[$formato];
        }
        return $ext;
    }
}
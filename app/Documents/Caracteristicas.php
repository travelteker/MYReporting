<?php

namespace App\Documents;

use \PhpOffice\PhpSpreadsheet\Style\Alignment as Align;

class Caracteristicas
{
    public function __construct(){}


    /**
     * Configurar los metadatos del documento que queremos descargar basados en la libreria PHPSPREADSHEET
     *
     * @param object $spreadsheet
     * @param string $metodo
     * @param array $data
     * @return void
     */
    public function configMeta($spreadsheet, $metodo, $data)
    {
        $owner = 'MYReporting';
        if(isset($data['owner'])){
            $owner = $data['owner'];
        }

        $title = 'Document API';
        if(isset($data['title'])){
            $title = $data['title'];
        }

        $keywords = 'SLIM3 PHP';
        if(isset($data['keywords'])){
            $keywords = $data['keywords'];
        }

        $category = 'Informe';
        if(isset($data['category'])){
            $category = $data['category'];
        }

        $spreadsheet->getProperties()
                    ->setCreator($owner)
                    ->setLastModifiedBy($owner)
                    ->setTitle($title)
                    ->setSubject($title)
                    ->setDescription($metodo)
                    ->setKeywords($keywords)
                    ->setCategory($category);
    }


    /**
     * Obtener las características asociadas a la fuente de la celda donde se escribe
     * @param string $tipo
     * @return void
     */
    public function getFont($tipo = '')
    {
        return [
            'bold' => true,
            'size' => 15
        ];
    }


    /**
     * Undocumented function
     *
     * @param string $tipo
     * @return void
     */
    public function getAlign($tipo = '')
    {
        return [
            'horizontal' => Align::HORIZONTAL_CENTER,
            'vertical' => Align::VERTICAL_CENTER,
        ];
    }
}
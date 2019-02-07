<?php

namespace App\Documents;

use App\Helpers\AuxFunctions as Aux;
use App\Documents\Caracteristicas;

class Document
{
    protected $caracteristicas;


    public function __construct()
    {
        $this->caracteristicas = new Caracteristicas;
    }


    /**
     * Método para especificar el tipo de guardado del fichero generado
     *
     * @param string $pathAbsoluteFile
     * @param object $writer
     * @param bool $saveInServer
     * @return void
     */
    public function saveFile($pathAbsoluteFile, $writer, $saveInServer)
    {
        //Salida de los datos
        if($saveInServer === false)
        {
            //Forzar la descargar desde el buffer de salida sin guardar en el servidor
            $writer->save('php://output');
        }
        else
        {
            //Guardar el fichero correspondiente en el PATH especificado
            $writer->save($pathAbsoluteFile);
            //Leer fichero y escribirlo al búfer de salida.
            readfile($pathAbsoluteFile);
        }
    }


    /**
     * Metodo para devolver las cabeceras de la respuesta de la petición realizada con la descarga del fichero
     *
     * @param objeto $response
     * @param string $nameFile
     * @return objeto
     */
    public function download($response, $nameFile)
    {
        $extFile = Aux::getExtensionFile($nameFile);
        $contentType = $this->contentTypeHeaderByFile($extFile);

        return $response->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Type', $contentType)
                ->withHeader('Content-Disposition', 'attachment; filename="' .$nameFile. '"')
                ->withHeader('Expires', 'Fri, 11 Nov 2011 11:11:11 GMT')                                  //Never cache
                ->withHeader('Cache-Control', 'max-age=0, private, no-store, no-cache, must-revalidate')  //Never cache
                ->withHeader('Pragma', 'public');
    }


    /**
     * Asignar ContentType para la cabecera HEADER de una respuesta HTTP
     *
     * @param string $extFile
     * @return string
     */
    public function contentTypeHeaderByFile($extFile)
    {
        $contentType = 'application/octet-stream';  //Valor por defecto para todas las descargas
        if(strtolower($extFile) === '.xlsx')
        {
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }
        return $contentType;
    }


    /**
     * Configurar los metadatos del fichero excel, con las caracteristicas personales declaradas o bien con valores por default
     *
     * @param objeto $spreadsheet
     * @param string $metodo
     * @param array $data
     * @return void
     */
    public function configMetaDataDocument($spreadsheet, $metodo, $data = array())
    {
        $this->caracteristicas->configMeta($spreadsheet, $metodo, $data);
    }


    /**
     * Configuracion estilo para la cabecera del report
     *
     * @return array
     */
    public function styleHeader()
    {
       return [
            'font' => $this->caracteristicas->getFont(),
            'alignment' => $this->caracteristicas->getAlign(),
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
    }


}
<?php


use Phinx\Migration\AbstractMigration;

class CreateReports extends AbstractMigration
{
    //Si no existe se ejecuta. Va actualizando el sistema con los nuevos cambios
    public function up()
    {
        //La creación de la tabla añadirá un campo 'id' como clave primaria y autonumérico
        $tabla = $this->table(
            'reports', 
            ['id' => 'id_report']       //Sobreescribimos el nombre de la clave primaria 'id' por defecto, por el nuevo nombre 'id_report', se mantiene el AUTO_INCREMENT
        );
        //Añadir columnas a la tabla que queremos crear

        $tabla->addColumn('nombre', 'string')                      //string -> varchar(255) default
              ->addColumn('descripcion', 'string')
              ->addColumn('ruta' ,'string',['limit' => 255])  
              ->addColumn('formato', 'string', ['limit' => 50])
              ->addTimestamps()                                  //Default timestamp con marca de tiempo now() -> crea 2 campos 'created_at' y 'updated_at'
              ->addIndex(['ruta','formato'], ['unique' => true, 'name' => 'idx_user_report'])   //creamos el índice indicamos tipo y nombre del indice
              ->save();
    }


    //Si existe lo revierte. Va deshaciendo lo creado
    public function down()
    {
        $this->table('reports')->drop()->save();
    }
}

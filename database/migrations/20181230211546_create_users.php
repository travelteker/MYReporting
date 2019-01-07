<?php


use Phinx\Migration\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    
    //Si no existe se ejecuta. Va actualizando el sistema con los nuevos cambios
    public function up()
    {
        //La creación de la tabla añadirá un campo 'id' como clave primaria y autonumérico
        $tabla = $this->table(
            'users', 
            ['id' => 'id_user']       //Sobreescribimos el nombre de la clave primaria 'id' por defecto, por el nuevo nombre 'id_user'; se mantiene el AUTO_INCREMENT
        );
        
        //Añadir columnas a la tabla que queremos crear
        $tabla->addColumn('name', 'string')                      //string -> varchar(255) default
              ->addColumn('email', 'string', ['limit' => 60])
              ->addColumn('password' ,'string',['limit' => 255])  //guardaremos un hash de 60 caracteres
              ->addTimestamps()                                  //Default timestamp con marca de tiempo now() -> crea 2 campos 'created_at' y 'updated_at'
              ->addIndex('email', ['unique' => true, 'name' => 'idx_users_email'])   //creamos el índice indicamos tipo y nombre del indice
              ->save();
    }


    //Si existe lo revierte. Va deshaciendo lo creado
    public function down()
    {
        $this->table('users')->drop()->save();
    }
}

<?php


use Phinx\Migration\AbstractMigration;

class CreateInventory extends AbstractMigration
{
    
    public function up()
    {
        //La creación de la tabla añadirá un campo 'id' como clave primaria y autonumérico
        $tabla = $this->table(
            'inventory', 
            ['id' => 'id_inventory']
        );
        
        //Añadir columnas a la tabla que queremos crear
        $tabla->addColumn('cod_inventory', 'integer')                      
              ->addColumn('name_product', 'string', ['limit' => 60])
              ->addColumn('total_in' ,'integer')  
              ->addColumn('area', 'string')
              ->addTimestamps()                                  //Default timestamp con marca de tiempo now() -> crea 2 campos 'created_at' y 'updated_at'
              ->addIndex(['cod_inventory', 'name_product'], ['unique' => true, 'name' => 'idx_inventory_cod_inventory'])   //creamos el índice indicamos tipo y nombre del indice
              ->save();
    }

    public function down()
    {
        $this->table('inventory')->drop()->save();
    }
}

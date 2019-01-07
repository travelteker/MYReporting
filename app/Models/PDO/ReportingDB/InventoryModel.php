<?php

namespace App\Models\PDO\ReportingDB;


class InventoryModel
{
    protected $db;


    public function __construct($conn)
    {
        $this->db = $conn;
    }

    

    public function productosInventario()
    {
        $sql = "SELECT i.cod_inventory, i.name_product, i.total_in, i.area ";
        $sql .= "FROM inventory i ";

        $query = $this->db->prepare($sql);
        $query->execute();
        $resultado = [];

        while($fila = $query->fetch(\PDO::FETCH_ASSOC))
        {
            $aux = [];
            $aux['producto'] = $fila['name_product'];
            $aux['total'] = $fila['total_in'];

            $resultado[$fila['cod_inventory']][$fila['area']][] = $aux;

            
            
        }
        return $resultado;
    }

}
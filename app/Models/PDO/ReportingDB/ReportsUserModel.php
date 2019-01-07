<?php

namespace App\Models\PDO\ReportingDB;


class ReportsUserModel
{
    protected $db;


    public function __construct($conn)
    {
        $this->db = $conn;
    }

    
    public function reportsByUser($iduser)
    {
        $sql = "SELECT r.nombre, r.descripcion, r.ruta, r.formato ";
        $sql .= "FROM reports r ";
        $sql .= "LEFT JOIN user_report ur ON ur.id_report = r.id_report ";
        $sql .= "WHERE ur.id_user = $iduser";

        $query = $this->db->prepare($sql);
        $query->execute();
        $datos = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        return $datos;
    }


    public function productosInventario()
    {
        $sql = "SELECT i.cod_inventory, i.name_product, i.total_in, i.area ";
        $sql .= "FROM inventory i ";

        $query = $this->db->prepare($sql);
        $query->execute();
        $resultado = [];

        while($fila = $query->fetch(\PDO::FETCH_ASSCO))
        {
            $aux = [];
            $aux['producto'] = $fila['name_product'];
            $aux['total'] = $fila['total_in'];

            $resultado[$fila['cod_inventory']][$fila['area']][] = $aux;

            
            
        }
        
        return $resultado;
    }

}
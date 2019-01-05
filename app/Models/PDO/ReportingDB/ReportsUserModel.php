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

}
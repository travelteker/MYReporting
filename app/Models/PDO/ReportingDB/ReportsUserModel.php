<?php

namespace App\Models\PDO\ReportingDB;


class ReportsUserModel
{
    protected $db;

    /**
     * Poner a disposición de la clase la instancia de conexión a la BD
     *
     * @param objeto $conn
     */
    public function __construct($conn)
    {
        $this->db = $conn;
    }

    
    /**
     * Obtener los reports disponibles que tiene un usuario
     *
     * @return array
     */
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
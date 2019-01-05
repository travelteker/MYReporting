<?php

#Tabla pivote o intermedia para relacion N:N entre tablas 'users' y 'reports'

use Phinx\Migration\AbstractMigration;

class CreateUserReport extends AbstractMigration
{
    public function up()
    {
        $tabla = $this->table('user_report' , ['id' => false, 'primary_key' => ['id_user', 'id_report']]);
        $tabla->addColumn('id_user', 'integer')
              ->addColumn('id_report', 'integer')
              ->addForeignKey('id_user', 'users', 'id_user', ['delete' => 'RESTRICT', 'update' => 'NO_ACTION', 'constraint' => 'fk_userreport_iduser_users'])
              ->addForeignKey('id_report', 'reports', 'id_report', ['delete' => 'RESTRICT', 'update' => 'NO_ACTION', 'constraint' => 'fk_userreport_idreport_reports'])
              ->save();
    }

    public function down()
    {
        $this->table('user_report')->drop()->save();
    }
}

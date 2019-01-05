<?php


use Phinx\Seed\AbstractSeed;

class ReportsSeeder extends AbstractSeed
{
    
    #http://localhost/slimCurso/public/reports/inventario_productos/excel

    public function run()
    {
        $data = [
            [
                'nombre'    => 'Productos inventariados',
                'descripcion' => 'Detalle de productos registrados según último inventario realizado',
                'ruta' => 'reports/inventario_productos/',
                'formato' => 'excel'
            ],
            [
                'nombre'    => 'Venta de productos',
                'descripcion' => 'Detalle de productos registrados según último inventario realizado',
                'ruta' => 'reports/venta_productos/',
                'formato' => 'excel'
            ],
        ];

        $users = $this->table('reports');
        $users->insert($data)
              ->save();
    }
}

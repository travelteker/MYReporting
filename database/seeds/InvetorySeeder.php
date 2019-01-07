<?php


use Phinx\Seed\AbstractSeed;

class InvetorySeeder extends AbstractSeed
{
    
    public function run()
    {     
        $totalFilas = 250;
        
        $faker = \Faker\Factory::create();

        $cod_inventory = $faker->ean8;

        for($i=0; $i < $totalFilas; $i++)
        {
            $fila[] = [
                'cod_inventory' => $cod_inventory,
                'name_product' => $faker->name,
                'total_in' => rand(1,30),
                'area' => $this->nameArea()
            ];
        }

        $inventory = $this->table('inventory');
        $inventory->insert($fila)
            ->save();
        
    }

    private function nameArea()
    {
        $areas = ['norte','sur','este','oeste'];
        $randomIndex = array_rand($areas);
        return $areas[$randomIndex];
    }
}

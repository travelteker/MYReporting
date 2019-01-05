<?php


use Phinx\Seed\AbstractSeed;

class UserReportSeeder extends AbstractSeed
{
    
    public function run()
    {
        $data = [
            [
                'id_user'    => 1,
                'id_report' => 1,
            ],
            [
                'id_user'    => 1,
                'id_report' => 2,
            ],
        ];

        $users = $this->table('user_report');
        $users->insert($data)
              ->save();
    }
}

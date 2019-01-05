<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'name'    => 'test',
                'email' => 'test@test.test',
                'password' => password_hash('test', PASSWORD_DEFAULT)
            ]
        ];

        $users = $this->table('users');
        $users->insert($data)
              ->save();
    }
}

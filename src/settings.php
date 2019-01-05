<?php

$settings = [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            'driver' => getenv('DRIVER'),
            'host' => getenv('HOST'),
            'port' => getenv('PORT'),
            'database' => getenv('DATABASE'),
            'username' => getenv('USER'),
            'password' => getenv('PASSWORD'),
            'charset' => getenv('CHARSET'),
            'collation' => getenv('COLLATION'),
            'prefix' => getenv('PREFIX')
        ] 
    ],
    
];


//Al iniciar la aplicación creamos la varialbe $environment
if(isset($environment) AND $environment == 'development')
{
    $settings['settings']['db']['options'] = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$settings['settings']['db']['charset']." COLLATE ".$settings['settings']['db']['collation']
    ];
}


return $settings;
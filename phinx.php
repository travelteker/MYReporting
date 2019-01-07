<?php


if(!defined('DIR_PHINX'))
{
    define('DIR_PHINX', 'database');
}

if(!defined('DIR_MIGRATIONS'))
{
    define('DIR_MIGRATIONS', 'migrations');
}

if(!defined('DIR_SEEDS'))
{
    define('DIR_SEEDS', 'seeds');
}

/* INFO: Las credenciales de la BD nos los tiene que facilitar el DevOps correspondiente para el sistema de testing, beta, production, etc ... */

/* ------------------- FUNCIONES AUXILIARES ------------------------ */

function searchEnvironment($host, $mis_entornos)
{
    $entorno = 'development';
    if(isset($mis_entornos[$host]))
    {
        $entorno = $mis_entornos[$host];
    }
    return $entorno;
}


function prepareEnvironmentStructure($entorno, $configPhinx)
{
    $aux = [
        'adapter' => getenv('DRIVER'),
        'host' => getenv('HOST'),
        'port' => getenv('PORT'),
        'name' => getenv('DATABASE'),
        'user' => getenv('USER'),
        'pass' => getenv('PASSWORD'),
        'charset' => getenv('CHARSET'),
        'collation' => getenv('COLLATION'),
        'table_prefix' => getenv('TABLE_PREFIX'),
        'table_suffix' => getenv('TABLE_SUFFIX')
    ];

    $configPhinx['environments'][$entorno] = $aux;
    return $configPhinx;
}


function webForbidden()
{
    $web = false;
    if(isset($_SERVER['REQUEST_URI']))
    {
        $web = true;
    }
    return $web;
    
}
    

/* ------------------ FIN FUNCIONES AUXILIARES ---------------------- */


if(webForbidden())
{
    die('No se permite el acceso a las migraciones desde la web');
}


//Desde sistemas o algún DevOps nos tienen que dar dicha información --> se puede obtener de variables de entorno del propio sistema operativo y no reflejarlas en fichero, xD!
$mis_entornos = [
    'beta.reporting.com' => 'testing',
    'reporting.com' => 'production' 
];

//Obtener el nombre del host donde se ejecuta el script
//Lo que no esté identificado se considera por defecto 'development'
$getHost = php_uname("n");
$entorno = searchEnvironment($getHost, $mis_entornos);

//Configuración parámetros PHINX
$configPhinx = [
    'paths' => [
        'migrations' => __DIR__.DIRECTORY_SEPARATOR . DIR_PHINX . DIRECTORY_SEPARATOR . DIR_MIGRATIONS,   //Path para guardar las migraciones
        'seeds' => __DIR__.DIRECTORY_SEPARATOR. DIR_PHINX .DIRECTORY_SEPARATOR . DIR_SEEDS                 //Path para guardar los seeds
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development'
    ]
];


//Cargar las variables del entorno solicitado para ejecutar las operaciones PHINX
$pathEntornoEjecucion = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'environments'.DIRECTORY_SEPARATOR.$entorno;
$dotenv = new Dotenv\Dotenv($pathEntornoEjecucion);
//Añadimos nuestra configuración a las variables globales "$_ENV", "$_SERVER"
$dotenv->load();

//Devolver array de configuracion para el entorno indicado
return prepareEnvironmentStructure($entorno, $configPhinx);

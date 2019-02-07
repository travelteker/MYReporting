<?php

//Identificar entorno desde donde se hace la request
$environment = null;
//Obtener todas las cabeceras de la petición
$cabeceras = apache_request_headers();
if(isset($cabeceras['Myrtkn'])) 
{
    //Para usar nuestra API todas las peticiones tienen que llevar por convención una cabecera del tipo 'Myrtkn' 
    //y con valor un string aleatorio
    $token = $cabeceras['Myrtkn'];
    $environment = 'api';
}

if(!isset($environment) AND isset($_SERVER['ENVIRONMENT']))
{
    $environment = $_SERVER['ENVIRONMENT'];
}

$entorno = __DIR__.DS.$environment;
if(!is_readable($entorno.DS.".env"))
{
    die("COD001A|Fatal Error|No se ha podido identificar el entorno|Aborting ...");
}


$dotenv = new Dotenv\Dotenv($entorno);
//Añadimos nuestras variables a las variables globales "$_ENV", "$_SERVER"
$dotenv->load();
<?php

//Identificar entorno desde donde se hace la request
$environment = null;
if(isset($_SERVER['ENVIRONMENT']))
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
<?php

session_start();

//Obtenemos la parametrizacion de la configuración
$settings = require __DIR__ . '/../config/settings.php';

//Instanciamos objeto slim
$app = new \Slim\App($settings);

// Añadir las dependencias a través de DI -> CONTENEDOR DEPENDENCIAS
require __DIR__ . '/../config/dependencies.php';


//------------------------------ MIDDLEWARES ------------------------------
//-------------------------------------------------------------------------
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
//Persistencia datos formulario registro user cuando se refresca la pagina
$app->add(new \App\Middleware\OldInputMiddleware($container));
//Para obtener los valores del csrf de los formularios --> seccion de registro usuario
//Así podemos inyectar los campos input type hidden necesarios usando una sentencia de twig; breve y limpio
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
//Csrf desde middleware general
$app->add($container->csrf);


//---------------- VALIDACIONES PARA FORMULARIOS -------------------------
//------------------------------------------------------------------------
use Respect\Validation\Validator as v;
v::with('App\\Validation\\Rules\\');


//Instanciamos las rutas necesarias
// ------------------------------ RUTAS -------------------------------------

require __DIR__ . '/../app/routes/routes.php';
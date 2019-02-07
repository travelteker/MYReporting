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


//--------------------------- PROTECCION CSRF ----------------------------
//------------------------------------------------------------------------
//Csrf desde middleware general --> para todas las rutas que se realicen al sistema
//$app->add($container->csrf);

//PERSONALIZAR LAS RUTAS WEB CON FORMULARIOS PARA QUE TENGAN PROTECCION CSRF EN VEZ DE PONERLO GLOBAL PARA TODAS LAS RUTAS
//ASÍ NO AFECTARA A LAS RUTAS DE LA API

//Añadir este middleware a aquellas rutas que necesiten validacion CSRF
//->add($container->get('csrf'));


//-------------------------- PROTECCION JWT -------------------------------
//-------------------------------------------------------------------------



$app->add(new \Tuupola\Middleware\JwtAuthentication([
            "attribute" => $container->get('settings')['myrtkn']['attribute'],
            "header" => $container->get('settings')['myrtkn']['header'],
            "secret" => $container->get('settings')['myrtkn']['secret'],
            "algorithm" => $container->get('settings')['myrtkn']['algorithm'],
    "rules" => [
        new \Tuupola\Middleware\JwtAuthentication\RequestPathRule([
            "path" => $container->get('settings')['myrtkn']['validPath'],   //Path donde se aplicará la validacion JWT
            "ignore" => [$container->get('settings')['myrtkn']['ignore']]   //Paths excluidos de la validacion JWT
            
        ]),
        new \Tuupola\Middleware\JwtAuthentication\RequestMethodRule([
            "ignore" => ["OPTIONS"]
        ])
    ],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES));
    }
]));






//---------------- VALIDACIONES PARA FORMULARIOS -------------------------
//------------------------------------------------------------------------
use Respect\Validation\Validator as v;
v::with('App\\Validation\\Rules\\');


//Instanciamos las rutas necesarias
// ------------------------------ RUTAS -------------------------------------

require __DIR__ . '/../app/routes/routes.php';
<?php

require __DIR__ . '/../vendor/autoload.php';

//Cargar constantes del sistema
require_once __DIR__ . '/../config/constantes.php';


//Cargar parametros de entorno usando DOTENV
require_once __DIR__ . '/../config/environments/entornos.php';




//Inicializamos la aplicacion
require __DIR__ . '/../bootstrap/app.php';

$app->run();


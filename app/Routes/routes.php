<?php

//Para proteger rutas comprobando si el usuario está logueado en el sistema o no
use App\Middleware\AuthMiddleware;

use App\Middleware\GuestMiddleware;

use App\Middleware\ReportingMiddleware;


//Para usar clases en las rutas, añadir las dependencias de cada controlador en 'dependencies.php' DI CONTAINER
$app->get('/','HomeController:index')->setName('home');





//El patrón de grupo puede estar vacío '', lo que permite la agrupación lógica de rutas que no comparten un patrón común.
$app->group('', function(){
     /*
        Nota dentro del cierre de grupo, $this se utiliza en lugar de $app. Slim vincula el cierre a la instancia de la aplicación para usted, 
        como es el caso de los enlaces de devolución de llamada de ruta con la instancia de contenedor.

        Cierre de grupo interno, $thi sestá vinculado a la instancia de Slim\App
        Cierre de ruta interior, $this está vinculado a la instancia de Slim\Container
    */

    //A las rutas de aplicación se les puede asignar un nombre 'setName(xxxxx)'. 
    //Esto es útil si desea generar mediante programación un URL a una ruta específica con el pathFor()método del enrutador
    //La ruta especifica se identificara en la vista mediante el uso de {{path_for()}}
    $this->get('/auth/signup','AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup','AuthController:postSignUp');

    //Rutas para el login de usuario
    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');

})->add(new GuestMiddleware($container))->add($container->get('csrf'));
//$container -- está declarado en el fichero src/dependencies.php y se carga antes que el fichero Routes/routes.php 




//El patrón de grupo puede estar vacío '', lo que permite la agrupación lógica de rutas que no comparten un patrón común.
$app->group('', function(){

    /*
        Nota dentro del cierre de grupo, $this se utiliza en lugar de $app. Slim vincula el cierre a la instancia de la aplicación para usted, 
        como es el caso de los enlaces de devolución de llamada de ruta con la instancia de contenedor.

        Cierre de grupo interno, $thi sestá vinculado a la instancia de Slim\App
        Cierre de ruta interior, $this está vinculado a la instancia de Slim\Container
    */

    //FIJARSE ENTONCES QUE USARMOS '$this' en vez de '$app'
    //Ruta para realizar logout y terminar la session
    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

    //Ruta para cambiar el password
    //Rutas para el login de usuario
    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');

})->add(new AuthMiddleware($container))->add($container->get('csrf'));
//Middleware para proteger rutas que necesitan que el usuario esté logueado previamente antes de acceder



//Rutas relacionadas con la vista reporting y para usarlas verificamos que el usuario esté logueado
$app->group('/reports', function() use ($container){

    //Una vez logueado el usuario podrá acceder a una lista de enlaces para descargar reports
    $this->get('/index','ReportsController:index')->setName('reports'); 
    
    $this->group('/v1', function() use ($container){
        $this->get('/{metodo}/{formato}','ReportsController:prepareFile')->setName('reports')->add(new ReportingMiddleware($container));
    });
    

})->add(new AuthMiddleware($container));


// ------------------------------------------ RUTAS DISPONIBLES API ------------------------------------------------------ //
// ----------------------------------------------------------------------------------------------------------------------- //

require_once ('routes_api.php');
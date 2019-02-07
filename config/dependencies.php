<?php
// DIC configuration
$container = $app->getContainer();


//Para utilizar ILLUMINATE ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();


$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};


$container['spreadsheet'] = function ($c) {
    return new PhpOffice\PhpSpreadsheet\Spreadsheet;
};

$container['writerXlsx'] = function ($c) {
    return new PhpOffice\PhpSpreadsheet\Writer\Xlsx($c->spreadsheet);
};


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//Para disponer de 'flashMessage' en los controladores
$container['flash'] = function($c){
    return new \Slim\Flash\Messages;
};


//Para tener disponible AUTH en los CONTROLADORES y EN LAS VISTAS YA QUE SE USA EN $CONTAINER['VIEW']
$container['auth'] = function($c){
    return new \App\Auth\Auth;
};


$container['myrtkn'] = function($c){
    return $c->get('settings')['myrtkn'];
};



//Para utilizar motor de plantillas TWIG
$container['view'] = function($c){
    $pathViews = __DIR__ . '/../views';
    $view = new \Slim\Views\Twig($pathViews, [
        'cache' => false    //o el path donde se van a cachear las vistas
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension($c->router, $c->request->getUri()));
    //Añadimos nuestra propias extensiones TWIG para tenerlas disponibles en las vistas
    $view->addExtension(new \App\ExtensionsViews\CsrfExtension($c['csrf']));
    
    //Disponer en las vistas de una variable global para identificar si el usuario está o no autenticado
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $c->auth->check(),
        'user' => $c->auth->user()
    ]);
    //En las vistas tendremnos disponibles los parametros 'check' y 'user' de la forma 'auth.check' y 'auth.user' respectivamente

    //Dispondremos de flash-message en las vistas
    $view->getEnvironment()->addGlobal('flash', $c->flash);
    
    return $view;
};


$container['validator'] = function($c){
    return new App\Validation\Validator;
};

$container['document'] = function($c){
    return new App\Documents\Document;
};


//Para tener disponible CSRF en los middlewares
//Personalizar parámetro y valor en caso de falle el CSRF, este valor estará disponible en la REQUEST para los middleware
$container['csrf'] = function($c){
    $guard = new \Slim\Csrf\Guard();
    $guard->setPersistentTokenMode(false);
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};


//Custom not found handler
//Override the default Not Found Handler before creating App
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $method = $request->getMethod();
        if(strtoupper($method) == 'GET')
        {
            $output = $c->view->render($response, 'errors/404.twig')->withStatus(404);
        }
        else
        {
            $output = $response->withJson(array('status' => 'KO', 'description' => 'Petición no válida, por favor contacte con soporte@soporte.com'));
        }
        return $output;
    };
};

// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------

$container['HomeController'] = function($c){
    return new \App\Controllers\HomeController($c);
};

$container['ReportsController'] = function($c){
    return new \App\Controllers\ReportsController($c);
};


$container['AuthController'] = function($c){
    return new \App\Controllers\Auth\AuthController($c);
};

$container['PasswordController'] = function($c){
    return new \App\Controllers\Auth\PasswordController($c);
};

$container['ApiController'] = function($c){
    return new \App\Controllers\ApiController($c);
};


// -----------------------------------------------------------------------------
// Models factories
// -----------------------------------------------------------------------------

//Para utilizar conector PDO directamente en los controladores
$container['connectPDOReporting'] = function ($c) {

    //Obtener los parámetros de configuración conexión desde los settings
    $db = $c->get('settings')['db'];

    $driver = $db['driver'];
    $host = $db['host'];
    $dbname = $db['database'];
    $username = $db['username'];
    $password = $db['password'];
    $charset = $db['charset'];
    $collate = $db['collation'];
    $dsn = "$driver:host=$host;dbname=$dbname;charset=$charset";
    $options = $db['options'];

    return new \PDO($dsn, $username, $password, $options);
};

$container['ReportsUser'] = function($c){
    return new \App\Models\PDO\ReportingDB\ReportsUserModel($c->connectPDOReporting);
};

$container['Inventory'] = function($c){
    return new \App\Models\PDO\ReportingDB\InventoryModel($c->connectPDOReporting);
};







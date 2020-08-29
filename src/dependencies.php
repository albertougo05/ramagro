<?php
// DIC configuration

$container = $app->getContainer();

// Setup Illuminate Database con Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Lo pasamos al contenedor...
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Auth
$container['auth'] = function ($container) {
    
    return new \App\Auth\Auth;
};

// Flash messages service
$container['flash'] = function ($container) {

    return new \Slim\Flash\Messages;
};

// Registra Twig views en el container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../templates', [
        'cache' => false,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    // Set valores para la autorizacion del usuario logeado
    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user'  => $container->auth->user(),
    ]);

    // Agrego soporte para mensajes Flash
    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};


// Csrf
$container['csrf'] = function ($container) {
    
    return new \Slim\Csrf\Guard;
};

// PDO
$container['pdo'] = function ($container) {
    
    return new \App\Pdo\PdoClass($container);
};



// REGISTRO DE MIDDLEWARE:

// Csrf
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add($container->csrf);



//
// REGISTRO DE CONTROLADORES:
//

// Home
$container['HomeController'] = function ($container) {

    return new \App\Controllers\HomeController($container);
};

// Utils
$container['utils'] = function ($container) {

    return new \App\Utils\Utils($container);
};

// Carta de porte
$container['CartadeporteController'] = function ($container) {

    return new \App\Controllers\Cartadeporte\CartadeporteController($container);
};

// Carta de porte - Genera Archivo Afip
$container['ArchivoafipController'] = function ($container) {

    return new \App\Controllers\Cartadeporte\ArchivoafipController($container);
};

// Carta de porte - Informe
$container['InformeController'] = function ($container) {

    return new \App\Controllers\Cartadeporte\InformeController($container);
};

// Firmas
$container['FirmasController'] = function ($container) {

    return new \App\Controllers\Firmas\FirmasController($container);
};




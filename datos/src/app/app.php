<?php
use Slim\Factory\AppFactory;
use DI\Container;
//use Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load();

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addRoutingMiddleware();

require 'config.php';
/*
 $app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
    "path" => ["/api"],
    "ignore" => ["/api/cliente", "/api/auth","/api/usr"],
    "secret" => ["acme" => $container->get('key')],
    "algorithm" => ["acme" => "HS256"]
])); 

/*$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => ["acme" => "supersecretkeyyoushouldnotcommittogithub"],
    "algorithm" => ["acme" => "HS256"],
]));*/

require 'conexion.php';
require_once 'routes.php';

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
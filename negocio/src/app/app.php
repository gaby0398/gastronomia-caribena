<?php

use Slim\Factory\AppFactory;
// use DI\Container;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load();

// Create Container using PHP-DI
//$container = new Container();

// Set container to create App with on AppFactory
//AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addRoutingMiddleware();
/*
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
    "path" => ["/api"],
    "ignore" => ["/api/auth","/api/cliente","/api/usr"],
    "secret" => ["acme" => $_ENV['KEY']],
    "algorithm" => ["acme" => "HS256"]
]));*/

// require 'config.php';
require_once 'routes.php';

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
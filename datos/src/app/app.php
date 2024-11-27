<?php

use Slim\Factory\AppFactory;
use DI\Container;
//use Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html');
$dotenv->load();

// Crear el Contenedor usando PHP-DI
$container = new Container();

// Configurar AppFactory para usar el contenedor
AppFactory::setContainer($container);

$app = AppFactory::create();

// Añadir el middleware de enrutamiento
$app->addRoutingMiddleware();

// **Añadir el Body Parsing Middleware aquí**
$app->addBodyParsingMiddleware();

/*
 // Middleware de Autenticación JWT (comentado)
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

// Incluir configuraciones adicionales
require 'config.php';

// Incluir conexión a la base de datos
require 'conexion.php';

// Incluir las rutas
require_once 'routes.php';

// Añadir el middleware de manejo de errores
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Ejecutar la aplicación
$app->run();
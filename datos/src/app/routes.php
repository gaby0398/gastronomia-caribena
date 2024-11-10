<?php
namespace App\controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

// require __DIR__ . '/../controllers/Cliente.php'
// Recurso Cliente
$app->group('/api',function(RouteCollectorProxy $api){
    $api->group('/cliente',function(RouteCollectorProxy $cliente){
        $cliente->post('', Cliente::class . ':create'); //Crea un cliente
        $cliente->get('/read[/{id}]', Cliente::class . ':read'); //Consulta todos los clientes
        $cliente->get('/filtro', Cliente::class . ':filtrar');
        $cliente->put('/{id}', Cliente::class . ':update'); //Actualiza todos los atributos, patch actualiza solo uno o algunos
        $cliente->delete('/{id}', Cliente::class . ':delete'); //Elimina un registro x su id
        // $cliente->get('/{id}', Cliente::class . ':buscar'); //Consulta un cliente x su id
    });

    $api->group('/administrador', function (RouteCollectorProxy $administrador) {
        $administrador->post('', Administrador::class . ':create');
        $administrador->get('/read[/{id}]', Administrador::class . ':read');
        $administrador->get('/filtro', Administrador::class . ':filtrar');
        $administrador->put('/{id}', Administrador::class . ':update');
        $administrador->delete('/{id}', Administrador::class . ':delete');
    });
    
  
    // Autenticacion
    $api->group('/auth',function(RouteCollectorProxy $auth){
        $auth->post('/iniciar', Auth::class . ':iniciar'); 
        $auth->patch('/cerrar/{idUsuario}', Auth::class . ':cerrar');
        $auth->patch('/refrescar', Auth::class . ':refrescar');
    });
    // Usuario
    $api->group('/usr',function(RouteCollectorProxy $usr){
        $usr->patch('/resetear/{idUsuario}', Usuario::class . ':resetearPassw'); 
        $usr->patch('/change/passw/{idUsuario}', Usuario::class . ':cambiarPassw');
        $usr->patch('/change/rol/{idUsuario}', Usuario::class . ':cambiarRol');
    });
    $api->group('/supervisor', function (RouteCollectorProxy $supervisor) {
        $supervisor->post('', supervisor::class . ':create');
        $supervisor->get('/read[/{id}]', supervisor::class . ':read');
        $supervisor->get('/filtro', supervisor::class . ':filtrar');
        $supervisor->put('/{id}', supervisor::class . ':update');
        $supervisor->delete('/{id}', supervisor::class . ':delete');
    });
    $api->group('/Products', function (RouteCollectorProxy $Products) {
        $Products->get('/read[/{id}]', Panes::class . ':read');
    });
    
    $api->group('/orders', function (RouteCollectorProxy $orders) {
        $orders->post('', orders::class . ':create');
        $orders->get('/read[/{id}]', orders::class . ':read');
        $orders->get('/filtro', orders::class . ':filtrar');
        $orders->put('/{id}', orders::class . ':update');
        $orders->delete('/{id}', orders::class . ':delete');
    });
    
    $api->group('/order_items', function (RouteCollectorProxy $order_items) {
        $order_items->post('', order_items::class . ':create');
        $order_items->get('/read[/{id}]', order_items::class . ':read');
        $order_items->get('/filtro', order_items::class . ':filtrar');
        $order_items->put('/{id}', order_items::class . ':update');
        $order_items->delete('/{id}', order_items::class . ':delete');
    });
    
    $api->group('/carts', function (RouteCollectorProxy $carts) {
        $carts->post('', carts::class . ':create');
        $carts->get('/read[/{id}]', carts::class . ':read');
        $carts->get('/filtro', carts::class . ':filtrar');
        $carts->put('/{id}', carts::class . ':update');
        $carts->delete('/{id}', carts::class . ':delete');
    });
    
    $api->group('/cart_items', function (RouteCollectorProxy $cart_items) {
        $cart_items->post('', cart_items::class . ':create');
        $cart_items->get('/read[/{id}]', cart_items::class . ':read');
        $cart_items->get('/filtro', cart_items::class . ':filtrar');
        $cart_items->put('/{id}', cart_items::class . ':update');
        $cart_items->delete('/{id}', cart_items::class . ':delete');
    });
});

   
//Pruebas Miedo, que nadie hace, nadie usa.....
// $app->get('/', function (Request $request, Response $response, $args) {
//     $response->getBody()->write("Hello world!");
//     return $response;
// });

// $app->get('/nombre/{nom}', function (Request $request, Response $response, $args) {
//     $nombre = $args['nom'];
//     $response->getBody()->write("Hola desde $nombre");
//     return $response;
// });*/

//ESTO FUE UNA PRUEBA POR JORDIdf
//prueva mia gaby claro que si 
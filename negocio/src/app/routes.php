<?php
namespace App\controllers;

use Slim\Routing\RouteCollectorProxy;
/* Hola Prueba ;v
// require __DIR__ . '/../controllers/Cliente.php';
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
        $usr->patch('/reset/passw/{idUsuario}', Usuario::class . ':resetPassw'); 
        $usr->patch('/change/passw/{idUsuario}', Usuario::class . ':changePassw');
        $usr->patch('/change/rol/{idUsuario}', Usuario::class . ':changeRol');
    });
    $api->group('/categories', function (RouteCollectorProxy $categories) {
        $categories->post('', categories::class . ':create');
        $categories->get('/read[/{id}]', categories::class . ':read');
        $categories->get('/filtro', categories::class . ':filtrar');
        $categories->put('/{id}', categories::class . ':update');
        $categories->delete('/{id}', categories::class . ':delete');
    });
    $api->group('/Products', function (RouteCollectorProxy $Products) {
        $Products->post('', products::class . ':create');
        $Products->get('/read[/{id}]', products::class . ':read');
        $Products->get('/filtro', products::class . ':filtrar');
        $Products->put('/{id}', products::class . ':update');
        $Products->delete('/{id}', products::class . ':delete');
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

});*/ 

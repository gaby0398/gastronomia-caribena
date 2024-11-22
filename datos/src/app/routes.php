<?php
namespace App\controller;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $api) {
    $api->group('/cliente', function (RouteCollectorProxy $cliente) {
        $cliente->post('', Cliente::class . ':create');
        $cliente->get('/read[/{id}]', Cliente::class . ':read');
        $cliente->get('/filtro', Cliente::class . ':filtrar');
        $cliente->put('/{id}', Cliente::class . ':update');
        $cliente->delete('/{id}', Cliente::class . ':delete');
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
        $usr->get('/getUser/{userParam}', Usuario::class . ':getUser');
    });

    $api->group('/supervisor', function (RouteCollectorProxy $supervisor) {
        $supervisor->post('', supervisor::class . ':create');
        $supervisor->get('/read[/{id}]', supervisor::class . ':read');
        $supervisor->get('/filtro', supervisor::class . ':filtrar');
        $supervisor->put('/{id}', supervisor::class . ':update');
        $supervisor->delete('/{id}', supervisor::class . ':delete');
    });

    $api->group('/comidas', function (RouteCollectorProxy $class) {
        $class->get('/read[/{id}]', Comidas::class . ':read');
        $class->delete('/{id}', Comidas::class . ':delete');
        $class->get('/filtro/{nombre_comidas}', Comidas::class . ':filtro');
        $class->put('/{id}',Comidas::class . ':update');
        $class->post('', Comidas::class . ':create');
    });

    

    $api->group('/plantas', function (RouteCollectorProxy $class) {
        $class->get('/read[/{id}]', Plantas::class . ':read');
        $class->delete('/{id}', Plantas::class . ':delete');
        $class->get('/filtro/{nombre_plantas}', Plantas::class . ':filtro');
        $class->put('/{id}',Plantas::class . ':update');
        $class->post('', Plantas::class . ':create');
    });

  
    $api->group('/restaurantes', function (RouteCollectorProxy $class) {
        $class->get('/read[/{id}]', Restaurantes::class . ':read');
        $class->delete('/{id}', Restaurantes::class . ':delete');
        $class->get('/filtro/{nombre_restaurantes}', Restaurantes::class . ':filtro');
        $class->put('/{id}',Restaurantes::class . ':update');
        $class->post('', Restaurantes::class . ':create');
    });
    
});
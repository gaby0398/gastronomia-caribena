<?php
namespace App\controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;


// require __DIR__ . '/../controllers/Cliente.php'
// Recurso Cliente


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
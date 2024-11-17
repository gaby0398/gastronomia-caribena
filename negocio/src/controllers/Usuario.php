<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Container\ContainerInterface;

class Usuario extends ServicioCURL
{
    private const ENDPOINT = "/usr";

    public function resetPassw(Request $request, Response $response, $args)
    {
        //$datos = $request->getBody();
        $url = $this::ENDPOINT . "/reset/{$args['idUsuario']}";
        $respA = $this->ejecutarCURL($url, 'PATCH');
        return $response->withStatus($respA['status']);
    }

    public function changePassw(Request $request, Response $response, $args)
    {
        $datos = $request->getBody();
        $url = $this::ENDPOINT . "/change/passw/{$args['idUsuario']}";
        $respA = $this->ejecutarCURL($url, 'PATCH', $datos);
        return $response->withStatus($respA['status']);
    }

    public function changeRol(Request $request, Response $response, $args)
    {
        $datos = $request->getBody();
        $url = $this::ENDPOINT . "/change/rol/{$args['idUsuario']}";
        $respA = $this->ejecutarCURL($url, 'PATCH', $datos);
        return $response->withStatus($respA['status']);
    }

    public function getUser(Request $request, Response $response, $args)
    {
        // Obtiene el parámetro desde los argumentos de la ruta
        $param = $args['userParam'] ?? null;

        // Si el parámetro no está definido, delega la responsabilidad a la API de Datos
        //$url = $this::ENDPOINT . "/getUser/" . urlencode($param);
        $url = $this::ENDPOINT . "/getUser/{$args['userParam']}";

        // Ejecuta la petición a la API de datos
        $respA = $this->ejecutarCURL($url, 'GET');

        // Devuelve la respuesta al cliente de la API de Negocios
        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus($respA['status']);
    }
}

<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Container\ContainerInterface;

class supervisor extends ServicioCURL
{
    private const ENDPOINT = "/supervisor";

    public function create(Request $request, Response $response, $args)
    {
        $datos = $request->getBody();
        $respA = $this->ejecutarCURL($this::ENDPOINT, 'POST', $datos);
        // print_r($respA);
        // die();
        return $response->withStatus($respA['status']);
    }

    public function read(Request $request, Response $response, $args)
    {
        $url = $this::ENDPOINT . '/read';
        if (isset($args['id'])) {
            $url .= "/{$args['id']}";
        }

        $respA = $this->ejecutarCURL($url, 'GET');

        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-type', 'Application/json')
            ->withStatus($respA['status']);
    }

    public function update(Request $request, Response $response, $args)
    {
        $datos = $request->getBody();
        $url = $this::ENDPOINT . "/{$args['id']}";
        $respA = $this->ejecutarCURL($url, 'PUT', $datos);
        return $response->withStatus($respA['status']);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $url = $this::ENDPOINT . "/{$args['id']}";
        $respA = $this->ejecutarCURL($url, 'DELETE');
        return $response->withStatus($respA['status']);
    }

    public function filtrar(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $url = $this::ENDPOINT . '/filtro?' . http_build_query($params);
        $respA = $this->ejecutarCURL($url, 'GET');
        $response->getBody()->write($respA['resp']);
        return $response->withHeader('Content-type', 'Application/json')
            ->withStatus($respA['status']);
    }
}

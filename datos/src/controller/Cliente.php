<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Cliente extends Persona {
    const RECURSO = 'cliente';
    const ROL = 4;

    function create(Request $request, Response $response, $args) {
        $body = json_decode($request->getBody(), true); // Cambiar '1' por 'true'
        $status = $this->createP(self::RECURSO, self::ROL, $body);
        return $response->withStatus($status);
    }

    function read(Request $request, Response $response, $args) {
        if (isset($args['id'])) {
            $resp = $this->readP(self::RECURSO, $args['id']);
        } else {
            $resp = $this->readP(self::RECURSO);
        }
        $response->getBody()->write(json_encode($resp['resp']));
        return $response->withHeader('Content-type', 'Application/json')->withStatus($resp['status']);
    }

    function update(Request $request, Response $response, $args) {
        $body = json_decode($request->getBody(), true); // Cambiar '1' por 'true'
        $status = $this->updateP(self::RECURSO, $body, $args['id']);
        return $response->withStatus($status);
    }

    function delete(Request $request, Response $response, $args) {
        $status = $this->deleteP(self::RECURSO, $args['id']);
        return $response->withStatus($status);
    }

    function filtrar(Request $request, Response $response, $args) {
        $datos = $request->getQueryParams();
        $res = $this->filtrarP($datos, self::RECURSO);
        $response->getBody()->write(json_encode($res['resp']));
        return $response->withHeader('Content-type', 'Application/json')->withStatus($res['status']);
    }
}


<?php

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Cliente{

    protected $container;

    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    function create(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());

        $sql = "INSERT INTO cliente( idCliente, nombre, apellido1, "
            . "apellido2, telefono, celular, direccion, correo, fechaIngreso) "
            . "VALUES ('$body->idCliente', '$body->nombre', '$body->apellido1', '$body->apellido2', "
            . "'$body->telefono', '$body->celular', '$body->direccion', '$body->correo', CURDATE())";

        $con = $this->conteiner->get('bd');
        $query = $con->prepare($sql);
        $query->execute();
        $status = $query->rowCount() > 0 ? 201 : 209;
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    function read(Request $request, Response $response, $args){
        $sql = "SELECT * FROM cliente";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute();
        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }
    
    function update(Request $request, Response $response, $args){
        $id = $args['id'];
        $body = json_decode($request->getBody());

        $sql = "UPDATE cliente SET "
            . "idCliente = '$body->idCliente', "
            . "nombre = '$body->nombre', "
            . "apellido1 = '$body->apellido1', "
            . "apellido2 = '$body->apellido2', "
            . "telefono = '$body->telefono', "
            . "celular = '$body->celular', "
            . "direccion = '$body->direccion', "
            . "correo = '$body->correo', "
            . "fechaIngreso = CURDATE() "
            . "where id = $id";

        $con = $this->conteiner->get('bd');
        $query = $con->prepare($sql);
        $query->execute();
        $status = $query->rowCount() > 0 ? 0 : 204;
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    function delete(Request $request, Response $response, $args){
        $id = $args['id'];
        $sql = "DELETE FROM cliente where id = $id";

        #busca la base de datos en los contenedores
        $con = $this->conteiner->get('bd');
        #prepara la consulta
        $query = $con->prepare($sql);
        #lo ejecuta
        $query->execute();

        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    function buscar(Request $request, Response $response, $args){
        $id = $args['id'];
        $sql = "SELECT * FROM cliente where id = $id";

        $con = $this->conteiner->get('bd');
        #prepara la consulta
        $query = $con->prepare($sql);
        #lo ejecuta
        $query->execute();
        //$res = $query->fetchAll();
        $res = $query->fetchAll();

        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }
}
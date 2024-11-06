<?php

namespace App\controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class Cliente{
    protected $container;
    #funciones magicas todo lo que empieza en __
    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    function create(Request $request, Response $response, $args){
        $body = json_decode($request->getBody(), 1); //1 para usarlo como arreglo
        $sql = "INSERT INTO cliente (";
        $values = "VALUES (";
        foreach ($body as $key => $value) {
            $sql .= $key . ', ';
            $values .= ":$key, ";
        }
        $values = substr($values, 0, -2) . "); ";
        $sql = substr($sql, 0, -2) . ") " . $values;

        // $sql = "INSERT INTO cliente (idCliente,nombre,apellido1,apellido2,telefono,"
        //     . "celular,direccion,correo)"

        //     . "VALUES(:idCliente, :nombre, :apellido1, :apellido2, "
        //     . ":telefono, :celular, :direccion, :correo);";

        // echo "<pre>";
        // var_dump($body);
        // echo "</pre>";
        // die();

        // die($sql);

        /*
        $query->bindParam(':idCliente', $body->idCliente, PDO::PARAM_STR);
        $query->bindParam(':nombre', $body->nombre);
        $query->bindParam(':apellido1', $body->apellido1);
        $query->bindParam(':apellido2', $body->apellido2);
        $query->bindParam(':telefono', $body->telefono);
        $query->bindParam(':celular', $body->celular);
        $query->bindParam(':direccion', $body->direccion);
        $query->bindParam(':correo', $body->correo);
        */

        $data = [];
        foreach ($body as $key => $value) {
            $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            $query = $con->prepare($sql);
            $query->execute($data);
            $id = $body['idUsuario'];
            $sql = "INSERT INTO usuario (idUsuario, correo, rol, passw) VALUES (:idUsuario, :correo, :rol, :passw);";

            // password_hash(string $password, string|int|null $algo, array $options = []): string

            $passw = password_hash($data['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);

            // die($passw);

            $query = $con->prepare($sql);
            $query->bindValue(":idUsuario", $id, PDO::PARAM_STR);
            $query->bindValue(":correo", $body['correo'], PDO::PARAM_STR);
            $query->bindValue(":rol", 4, PDO::PARAM_INT);
            $query->bindValue(":passw", $passw);
            $query->execute();
            $con->commit();
            $status = 201;
        } catch (\PDOException $e) {
            // echo ($e->getCode() . '<br>');
            // echo ($e->getMessage());
            $status = $e->getCode() == 23000 ? 409 : 500;
            $con->rollback();
        }
        // $status = $query->rowCount() > 0 ? 201 : 409; //Conflicto
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    function read(Request $request, Response $response, $args){
        $sql = "SELECT * FROM cliente ";

        if (isset($args['id'])) {
            // $sql .= "WHERE id = {$args['id']}";
            $sql .= "WHERE id= :id";
        }
        // if (isset($args['id'])) {
        //     $sql = "SELECT * FROM cliente WHERE id = $args{['id']}";
        // }else {
        //     $sql = "SELECT * FROM cliente";
        // }
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        if (isset($args['id'])) {
            $query->execute(['id' => $args['id']]);
        } else {
            $query->execute();
        }
        $res = $query->fetchAll();
        // $res = $query->fetch(PDO::FETCH_ASSOC);
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }

    function update(Request $request, Response $response, $args){
        /* Sintaxis de un UPDATE
        UPDATE nombre_tabla
        SET campo1 = valor1, campo2 = valor2, ...
        WHERE condicion;
        */
        // $id = $args['id'];
        $body = json_decode($request->getBody());

        if (isset($body->id)) {
            unset($body->id);
        }

        $sql = "UPDATE cliente SET ";
        foreach ($body as $key => $value) {
            $sql .= "$key = :$key, ";
        }
        // $sql .= "fechaIngreso = CURDATE() WHERE id = $id;";
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE id = :id;";

        // die($sql);

        // ."idCliente = '$body->idCliente', "
        // ."nombre = '$body->nombre', "
        // ."apellido1 = '$body->apellido1', "
        // ."apellido2 = '$body->apellido2', "
        // ."telefono= '$body->telefono', "
        // ."celular = '$body->celular', "
        // ."direccion = '$body->direccion', "
        // ."correo = '$body->correo', "
        // ."fechaIngreso = CURDATE() "
        // . "WHERE id = $id";

        // $data = [];
        // foreach ($body as $key => $value) {
        //     $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        // }

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        foreach ($body as $key => $value) {
            $query->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        $query->bindValue(':id', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204; //Conflicto
        $query = null;
        $con = null;
        return $response->withStatus($status);
    }

    function delete(Request $request, Response $response, $args)    {
        //Sanitizar
        // $id = "' ; DROP database taller; -- OR 1 = 1 --";
        $sql = "DELETE FROM cliente WHERE id = :id";
        // $sql = "DELETE FROM cliente WHERE id = $id OR 1 = 1";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindValue(":id", $args['id'], PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        // $response->getBody()->write(json_encode($res));
        return $response->withStatus($status); //200 -> OK
    }

    function filtrar(Request $request, Response $response, $args){
        $datos = $request->getQueryParams();
        // select * from cliente where nombre LIKE '%{$datos['nombre']}%'
        // AND apellido1 LIKE '%{$datos['apellido1']}'
        // AND apellido2 LIKE '%{$datos['apellido2']}'
        // die($datos);
        $sql = "SELECT * FROM cliente WHERE ";
        foreach ($datos as $key => $value) {
            $sql .= "$key LIKE :$key AND ";
        }
        $sql = rtrim($sql, 'AND ') . ';';
        // die($sql);
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", "%$value%", PDO::PARAM_STR);
        }
        // $query->bindParam(':nombre', "'%" . $datos['nombre'] . "%'",PDO::PARAM_STR);
        // $query->bindParam(':apellido1',$datos['apellido1']);
        // $query->bindParam(':apellido2',$datos['apellido2']);

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
}

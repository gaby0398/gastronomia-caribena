<?php

namespace App\controller;

use Psr\Container\ContainerInterface;
use PDO;

class Persona{
    protected $container;
    #funciones magicas todo lo que empieza en __
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    function createP($recurso, $rol, $datos) {
        $sql = "INSERT INTO $recurso (";
        $values = "VALUES (";
        foreach ($datos as $key => $value) {
            $sql .= $key . ', ';
            $values .= ":$key, ";
        }
        $values = substr($values, 0, -2) . "); ";

        $sql = substr($sql, 0, -2) . ") " . $values;

        $data = [];
        foreach ($datos as $key => $value) {
            $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            $query = $con->prepare($sql);
            $query->execute($data);
            $id = $datos['idUsuario'];
            $sql = "INSERT INTO usuario (idUsuario, correo, rol, passw) VALUES (:idUsuario, :correo, :rol, :passw);";
            $passw = password_hash($data['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);
            $query = $con->prepare($sql);

            $query->bindValue(":idUsuario", $id, PDO::PARAM_STR);
            $query->bindValue(":correo", $datos['correo'], PDO::PARAM_STR);
            $query->bindValue(":rol",$rol, PDO::PARAM_INT);
            $query->bindValue(":passw", $passw);

            $query->execute();
            $con->commit();
            $status = 201;
        } catch (\PDOException $e) {
            $status = $e->getCode() == 23000 ? 409 : 500;
            $con->rollback();
        }

        $query = null;
        $con = null;
        return $status;
    }

    function readP($recurso, $id = null) {
        $sql = "SELECT * FROM $recurso ";
        if ($id != null) {
            $sql .= "WHERE id= :id";
        }
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        if ($id != null) {
            $query->execute(['id' => $id]);
        } else {
            $query->execute();
        }
        $resp['resp'] = $query->fetchAll();
        $resp['status'] = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return $resp;
    }

    function updateP($recurso, $datos, $id) {

        //PARA NO MANDAR LA ID del cliente para que n se mdifque 
        $sql = "UPDATE $recurso SET ";
        foreach ($datos as $key => $value) {
            $sql .= "$key = :$key, ";
            
        }
        
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE id = :id;";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute(); //EJECUTAMOS LA CONSULTA
        
        $status =$query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return $status;
    }

    function deleteP($recurso,$id) {
        $sql = "DELETE FROM $recurso WHERE id = :id";
        // $sql = "DELETE FROM cliente WHERE id = $id OR 1 = 1";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;

        // $response->getBody()->write(json_encode($res));

        return($status); //200 -> OK
    }

    function filtrarP( $datos,$recurso) {
     
        $sql = "SELECT * FROM $recurso WHERE ";
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
        $res['resp'] = $query->fetchAll();
        $res['status'] = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;
        return $res;
    }
}

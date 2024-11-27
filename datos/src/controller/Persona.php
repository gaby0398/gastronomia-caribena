<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use PDO;

class Persona {
    protected $container;

    public function __construct(ContainerInterface $c) {
        $this->container = $c;
    }

    function createP($recurso, $rol, $datos) {
        $sql = "INSERT INTO $recurso (";
        $values = "VALUES (";
        foreach ($datos as $key => $value) {
            $sql .= "$key, ";
            $values .= ":$key, ";
        }
        $values = rtrim($values, ', ') . ");";
        $sql = rtrim($sql, ', ') . ") " . $values;

        // Sanitizar los datos
        $data = [];
        foreach ($datos as $key => $value) {
            $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            // Insertar en la tabla del recurso
            $query = $con->prepare($sql);
            $query->execute($data);

            // Insertar en la tabla usuario
            $id = $datos['idUsuario'];
            $sql = "INSERT INTO usuario (idUsuario,alias, correo, rol, passw) VALUES (:idUsuario, :alias, :correo, :rol, :passw);";
            $passw = password_hash($datos['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);
            $query = $con->prepare($sql);

            $query->bindValue(":idUsuario", $id, PDO::PARAM_STR);
            $query->bindValue(":alias", $datos['alias'], PDO::PARAM_STR);
            $query->bindValue(":correo", $datos['correo'], PDO::PARAM_STR);
            $query->bindValue(":rol", $rol, PDO::PARAM_INT);
            $query->bindValue(":passw", $passw);
            $query->execute();

            $con->commit();
            $status = 201; // Creación exitosa
        } catch (\PDOException $e) {
            $status = $e->getCode() == 23000 ? 409 : 500;
            $con->rollback();
        }

        return $status;
    }

    function readP($recurso, $id = null) {
        $sql = "SELECT * FROM $recurso";
        if ($id) {
            $sql .= " WHERE id = :id";
        }

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        if ($id) {
            $query->execute(['id' => $id]);
        } else {
            $query->execute();
        }

        $resp['resp'] = $query->fetchAll();
        $resp['status'] = $query->rowCount() > 0 ? 200 : 204;

        return $resp;
    }

    function updateP($recurso, $datos, $id) {
        $sql = "UPDATE $recurso SET ";
        foreach ($datos as $key => $value) {
            $sql .= "$key = :$key, ";
        }
        $sql = rtrim($sql, ', ') . " WHERE id = :id";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", $value, PDO::PARAM_STR);
        }

        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204;

        return $status;
    }

    function deleteP($recurso, $id) {
        $sql = "DELETE FROM $recurso WHERE id = :id";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204;

        return $status;
    }

    function filtrarP($datos, $recurso) {
        $sql = "SELECT * FROM $recurso WHERE ";
        foreach ($datos as $key => $value) {
            $sql .= "$key LIKE :$key AND ";
        }
        $sql = rtrim($sql, 'AND ') . ';';

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        foreach ($datos as $key => $value) {
            $query->bindValue(":$key", "%$value%", PDO::PARAM_STR);
        }

        $query->execute();
        $res['resp'] = $query->fetchAll();
        $res['status'] = $query->rowCount() > 0 ? 200 : 204;

        return $res;
    }
}
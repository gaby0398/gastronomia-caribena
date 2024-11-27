<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use PDO;

class Persona
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    function createP($recurso, $rol, $datos)
    {
        // Sanitizar los datos
        $data = [];
        foreach ($datos as $key => $value) {
            $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            // Insertar en la tabla usuario primero
            $alias = $data['alias'];
            $correo = $data['correo'];
            $sqlUsuario = "INSERT INTO usuario (alias, correo, rol, passw) VALUES (:alias, :correo, :rol, :passw);";
            // Definir la contraseña por defecto (por ejemplo, el alias)
            $passw = password_hash($alias, PASSWORD_BCRYPT, ['cost' => 10]);
            $queryUsuario = $con->prepare($sqlUsuario);

            $queryUsuario->bindValue(":alias", $alias, PDO::PARAM_STR);
            $queryUsuario->bindValue(":correo", $correo, PDO::PARAM_STR);
            $queryUsuario->bindValue(":rol", $rol, PDO::PARAM_INT);
            $queryUsuario->bindValue(":passw", $passw);
            $queryUsuario->execute();

            // Insertar en la tabla del recurso
            $sqlRecurso = "INSERT INTO $recurso (";
            $values = "VALUES (";
            foreach ($data as $key => $value) {
                $sqlRecurso .= "$key, ";
                $values .= ":$key, ";
            }
            $values = rtrim($values, ', ') . ");";
            $sqlRecurso = rtrim($sqlRecurso, ', ') . ") " . $values;

            $queryRecurso = $con->prepare($sqlRecurso);
            $queryRecurso->execute($data);

            $con->commit();
            $status = 201; // Creación exitosa
        } catch (\PDOException $e) {
            $con->rollback();
            $status = $e->getCode() == 23000 ? 409 : 500;
        }

        return $status;
    }

    function readP($recurso, $id = null)
    {
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

    function updateP($recurso, $datos, $id)
    {
        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            // Obtener el alias y correo actuales del recurso
            $sqlSelect = "SELECT alias, correo FROM $recurso WHERE id = :id";
            $querySelect = $con->prepare($sqlSelect);
            $querySelect->bindValue(":id", $id, PDO::PARAM_INT);
            $querySelect->execute();
            $currentData = $querySelect->fetch(PDO::FETCH_ASSOC);

            if (!$currentData) {
                throw new \Exception("Registro no encontrado", 404);
            }

            $aliasAnterior = $currentData['alias'];
            $correoAnterior = $currentData['correo'];

            // Construir la consulta de actualización
            $sql = "UPDATE $recurso SET ";
            foreach ($datos as $key => $value) {
                $sql .= "$key = :$key, ";
            }
            $sql = rtrim($sql, ', ') . " WHERE id = :id";

            // Actualizar el recurso
            $query = $con->prepare($sql);
            foreach ($datos as $key => $value) {
                $query->bindValue(":$key", $value, PDO::PARAM_STR);
            }
            $query->bindValue(":id", $id, PDO::PARAM_INT);
            $query->execute();

            // Si alias o correo han cambiado, actualizar en usuario
            $aliasNuevo = $datos['alias'] ?? $aliasAnterior;
            $correoNuevo = $datos['correo'] ?? $correoAnterior;

            if ($aliasNuevo != $aliasAnterior || $correoNuevo != $correoAnterior) {
                $sqlUpdateUsuario = "UPDATE usuario SET alias = :aliasNuevo, correo = :correoNuevo WHERE alias = :aliasAnterior OR correo = :correoAnterior";
                $queryUpdateUsuario = $con->prepare($sqlUpdateUsuario);
                $queryUpdateUsuario->bindValue(":aliasNuevo", $aliasNuevo, PDO::PARAM_STR);
                $queryUpdateUsuario->bindValue(":correoNuevo", $correoNuevo, PDO::PARAM_STR);
                $queryUpdateUsuario->bindValue(":aliasAnterior", $aliasAnterior, PDO::PARAM_STR);
                $queryUpdateUsuario->bindValue(":correoAnterior", $correoAnterior, PDO::PARAM_STR);
                $queryUpdateUsuario->execute();
            }

            $con->commit();
            $status = 200; // Actualización exitosa
        } catch (\PDOException $e) {
            $con->rollback();
            $status = $e->getCode() == 23000 ? 409 : 500;
        } catch (\Exception $e) {
            $con->rollback();
            $status = $e->getCode();
        }

        return $status;
    }

    function deleteP($recurso, $id)
    {
        $sql = "DELETE FROM $recurso WHERE id = :id";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindValue(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $status = $query->rowCount() > 0 ? 200 : 204;

        return $status;
    }

    function filtrarP($datos, $recurso)
    {
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
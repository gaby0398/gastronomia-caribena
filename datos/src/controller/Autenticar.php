<?php

namespace App\controller;

use Psr\Container\ContainerInterface;
use PDO;

class Autenticar
{
    protected $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function autenticar($usuario, $passw, $cambioPassw = false)
    {
        // Modificar la consulta SQL para usar marcadores de posición únicos
        $sql = "SELECT * FROM usuario WHERE alias = :alias OR correo = :correo";

        try {
            $con = $this->container->get('bd');
            $query = $con->prepare($sql);
            // Vincular cada marcador de posición con su valor correspondiente
            $query->bindValue(':alias', $usuario, PDO::PARAM_STR);
            $query->bindValue(':correo', $usuario, PDO::PARAM_STR);
            $query->execute();
            $datos = $query->fetch();

            if ($datos && password_verify($passw, $datos->passw)) {
                $retorno = ["rol" => $datos->rol];

                $recurso = match ($datos->rol) {
                    1 => "administrador",
                    2 => "supervisor",
                    3  => "cliente",
                    default => null,
                };

                if (!$recurso) {
                    throw new \Exception("Rol de usuario no válido.");
                }

                if (!$cambioPassw) {
                    $sqlUpdate = "UPDATE usuario SET ultimoAcceso = NOW() WHERE alias = :alias OR correo = :correo";
                    $queryUpdate = $con->prepare($sqlUpdate);
                    $queryUpdate->bindValue(":alias", $datos->alias, PDO::PARAM_STR);
                    $queryUpdate->bindValue(":correo", $datos->correo, PDO::PARAM_STR);
                    $queryUpdate->execute();
                }

                $sqlNombre = "SELECT nombre FROM usuario WHERE alias = :alias OR correo = :correo";
                $queryNombre = $con->prepare($sqlNombre);
                $queryNombre->bindValue(":alias", $datos->alias, PDO::PARAM_STR);
                $queryNombre->bindValue(":correo", $datos->correo, PDO::PARAM_STR);
                $queryNombre->execute();
                $nombreDatos = $queryNombre->fetch();

                if (!$nombreDatos) {
                    throw new \Exception("Nombre no encontrado en la tabla Usuario.");
                }

                $retorno["nombre"] = $nombreDatos->nombre;
                return $retorno;
            }
            return null;
        } catch (\PDOException $e) {
            // Registrar el error en los logs
            error_log("Error en autenticar (PDOException): " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log("Error en autenticar (Exception): " . $e->getMessage());
            return null;
        } finally {
            $query = null;
            $con = null;
        }
    }
}
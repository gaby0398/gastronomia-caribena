<?php
namespace App\controller;
use Psr\Container\ContainerInterface;
use PDO;

class Autenticar {
    protected $container;
    public function __construct(ContainerInterface $c){
        $this->container = $c;

    }

    public function autenticar($usuario, $passw, $cambioPassw = false){
        $sql = "SELECT * FROM usuario WHERE idUsuario = :idUsuario";
        $sql .= " OR correo = :idUsuario ";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindValue(':idUsuario', $usuario, PDO::PARAM_STR);
        $query->execute();
        $datos = $query->fetch();

        /* var_dump($datos);
        die(); */

        if ($datos && password_verify($passw, $datos->passw)){
            $retorno = ["rol" => $datos->rol];

            $recurso = match ($datos->rol) {
                1 => "administrador",
                2 => "supervisor",
                3 => "tecnico",
                4 => "cliente",
            };
            if(!$cambioPassw){
                $sql = "UPDATE usuario SET ultimoAcceso = CURDATE()";
                $sql .= " WHERE idUsuario = :idUsuario OR correo = :idUsuario";
                $query = $con->prepare($sql);
                $query->bindValue(":idUsuario", $datos->idUsuario);
                $query->execute();
            }
            

            $sql = "SELECT nombre FROM $recurso WHERE idUsuario = :idUsuario";
            $sql .= " OR correo = :idUsuario";
            $query = $con->prepare($sql);
            $query->bindValue(":idUsuario", $datos->idUsuario);
            $query->execute();
            $datos = $query->fetch();
            $retorno["nombre"] = $datos->nombre;
        }
        $query = null;
        $con = null;
        return isset($retorno) ? $retorno : null;
    }

}
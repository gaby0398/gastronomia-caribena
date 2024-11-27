<?php

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Usuario extends Autenticar
{
    protected $container;
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }
    public function editarUsuario(string $idUsuario, int $rol = -1, string $passw = "")
    {
        $sql = $rol == -1 ? "UPDATE usuario SET passw = '$passw'" : "UPDATE usuario SET rol = '$rol'";
        $sql .= " WHERE idUsuario = :idUsuario OR correo = :idUsuario";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute(["idUsuario" => $idUsuario]);
        $afec = $query->rowCount();
        $query = null;
        $con = null;
        return $afec;
    }

    public function cambiarPassw(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        if ($this->autenticar($args['idUsuario'], $body->passw, true)) {
            $passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 10]);
            $datos = $this->editarUsuario(idUsuario: $args['idUsuario'], passw: $passwN);
            $status = 200;
        } else {
            $status = 401;
        }
        return $response->withStatus(200);
    }

    public function resetearPassw(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        $passw = password_hash($args['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);
        $status = $this->editarUsuario(idUsuario: $args['idUsuario'], passw: $passw) == 0 ? 204 : 200;
        return $response->withStatus($status);
    }

    /**
     * Cambiar el rol de un usuario identificado por alias o correo.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function cambiarRol(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());

        // Validar que se haya proporcionado el nuevo rol
        if (!isset($body->rol)) {
            $response->getBody()->write(json_encode(['error' => 'El nuevo rol no fue proporcionado']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $nuevoRol = $body->rol;
        $aliasOrCorreo = $args['aliasOrCorreo']; // Asegúrate de que el parámetro de la ruta se llama aliasOrCorreo

        try {
            $con = $this->container->get('bd');

            // Preparar la llamada al procedimiento almacenado
            $stmt = $con->prepare("CALL cambiarRol(:p_aliasOrCorreo, :p_nuevoRol, @p_status)");
            $stmt->bindParam(':p_aliasOrCorreo', $aliasOrCorreo, PDO::PARAM_STR);
            $stmt->bindParam(':p_nuevoRol', $nuevoRol, PDO::PARAM_INT);
            $stmt->execute();

            // Obtener el valor de @p_status
            $result = $con->query("SELECT @p_status as status")->fetch(PDO::FETCH_ASSOC);
            $status = isset($result['status']) ? (int)$result['status'] : 500;

            // Manejar la respuesta según el status
            switch ($status) {
                case 200:
                    $response->getBody()->write(json_encode(['message' => 'Rol actualizado exitosamente']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                case 400:
                    $response->getBody()->write(json_encode(['error' => 'Rol inválido']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                case 404:
                    $response->getBody()->write(json_encode(['error' => 'Usuario no encontrado']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
                case 500:
                default:
                    $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(500);
            }
        } catch (\PDOException $e) {
            // Registrar el error en los logs
            error_log("Error al cambiar rol: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
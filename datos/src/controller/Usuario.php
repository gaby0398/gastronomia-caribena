<?php
namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Usuario extends Autenticar{
    protected $container;
    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }
    public function editarUsuario(string $idUsuario, int $rol = -1, string $passw = ""){
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

    public function cambiarPassw(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        if($this->autenticar($args['idUsuario'], $body->passw, true)){
            $passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 10]);
            $datos = $this->editarUsuario(idUsuario: $args['idUsuario'], passw: $passwN);
            $status = 200;
            }else {
                $status = 401;
            }
        return $response->withStatus(200);
    }

    public function resetearPassw(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $passw = password_hash($args['idUsuario'], PASSWORD_BCRYPT, ['cost' => 10]);
        $status = $this->editarUsuario(idUsuario: $args['idUsuario'], passw: $passw) == 0 ? 204 : 200;
        return $response->withStatus($status);
    }

    public function cambiarRol(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $status = $this->editarUsuario(idUsuario: $args['idUsuario'], rol: $body->rol)
            == 0 ? 204 : 200;
        return $response->withStatus($status);
    }

    public function getUser(Request $request, Response $response, $args)
    {
        // Obtiene el parámetro desde los argumentos de la ruta (args)
        $param = $args['userParam'] ?? null;

        // Verifica si el parámetro fue proporcionado; si no, devuelve un error
        if (!$param) {
            $response->getBody()->write(json_encode(['error' => 'Parámetro userParam no proporcionado']));
            return $response
                ->withHeader('Content-type', 'application/json') // Indica que la respuesta es JSON
                ->withStatus(400); // Devuelve un código HTTP 400 (Bad Request)
        }

        // Define la consulta SQL para llamar al procedimiento almacenado
        $sql = "CALL getUser(:userParam)";
        $con = $this->container->get('bd'); // Obtiene la conexión a la base de datos desde el contenedor

        try {
            // Prepara la consulta SQL con el procedimiento almacenado
            $query = $con->prepare($sql);
            // Vincula el parámetro recibido al parámetro del procedimiento almacenado
            $query->bindParam(':userParam', $param, PDO::PARAM_STR);
            // Ejecuta la consulta
            $query->execute();
            // Obtiene el resultado de la consulta como un arreglo asociativo
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Si se encontró un resultado, lo devuelve en el cuerpo de la respuesta
            if ($result) {
                $response->getBody()->write(json_encode($result)); // Escribe el resultado en formato JSON
                $status = 200; // Código HTTP 200 (OK)
            } else {
                // Si no se encuentra ningún resultado, devuelve un error
                $response->getBody()->write(json_encode(['error' => 'Usuario no encontrado']));
                $status = 404; // Código HTTP 404 (Not Found)
            }
        } catch (\PDOException $e) {
            // Maneja errores de la base de datos
            $response->getBody()->write(json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]));
            $status = 500; // Código HTTP 500 (Internal Server Error)
        }

        // Retorna la respuesta con el encabezado de tipo JSON y el código de estado correspondiente
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus($status);
    }



    /******************* 

    public function editarRol(string $idUsuario, int $rol)
    {
        $sql = "UPDATE usuario SET rol = :rol WHERE id = :id OR correo = :id";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute([
            ":rol" => $rol,
            ":id" => $idUsuario
        ]);
        $afec = $query->rowCount();
        $query = null;
        $con = null;
        return $afec;
    }

    public function cambiarRol(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true); // Decodificar como arreglo asociativo

        // Validar que el rol esté presente y sea un entero válido
        if (!isset($body['rol']) || !is_int($body['rol'])) {
            $error = ['error' => 'El parámetro "rol" es requerido y debe ser un entero válido.'];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        $idUsuario = $args['idUsuario'];
        $rol = $body['rol'];

        try {
            $afectados = $this->editarRol($idUsuario, $rol);

            if ($afectados > 0) {
                // Rol actualizado exitosamente
                $success = ['message' => 'Rol actualizado exitosamente.'];
                $response->getBody()->write(json_encode($success));
                return $response
                    ->withStatus(200)
                    ->withHeader('Content-Type', 'application/json');
            } else {
                // No se encontró el usuario o el rol ya es el mismo
                $error = ['error' => 'Usuario no encontrado o el rol proporcionado es el mismo que el actual.'];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'application/json');
            }
        } catch (\PDOException $e) {
            // Manejo de errores de la base de datos
            $error = ['error' => 'Error al actualizar el rol.', 'details' => $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Manejo de otros errores
            $error = ['error' => 'Ocurrió un error inesperado.', 'details' => $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }



    public function editarPassw(string $idUsuario, string $passw)
    {
        $sql = "UPDATE usuario SET passw = :passw WHERE id = :id OR correo = :id";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute([
            ":passw" => $passw,
            ":id" => $idUsuario
        ]);
        $afec = $query->rowCount();
        $query = null;
        $con = null;
        return $afec;
    }*/
}
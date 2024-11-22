<?php
namespace App\controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class Auth extends Autenticar {
    protected $container;
    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    private function accederToken(string $proc, string $idUsuario, string $tkRef = ""){
        //valor, comparación y valor y tipo
        $sql = $proc === "modificar" ? "SELECT modificarToken" : "CALL verificarTokenR";
        $sql .= "(:idUsuario, :tkRef)";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute(['idUsuario' => $idUsuario, 'tkRef' => $tkRef]);
        $datos = $proc === 'modificar' ? $query->fetch(PDO::FETCH_NUM) : $query->fetchColumn();
        $query = null;
        $con = null;
        return $datos;
    }

    private function modificarToken(string $idUsuario, string $tkRef = ""){
        return $this->accederToken('modificar', $idUsuario, $tkRef);
    }

    private function verificarToken ($idUsuario, $tkRef){
        return $this->accederToken('verificar', $idUsuario, $tkRef);
    }

    public function cerrar(Request $request, Response $response, $args)
    {
        $this->modificarToken(idUsuario: $args['idUsuario']);
        return $response->withStatus(200);
    }
        
    private function generarToken(string $idUsuario, int $rol, string $nombre){
        $key = $this->container->get("key");
        $payload = [
            'iss' => $_SERVER['SERVER_NAME'],
            'iat' => time(),
            'exp' => time() + 1100,
            'sub' => $idUsuario, 
            'rol' => $rol,
            'nom' => $nombre
        ];
        $payloadRef = [
            'iss' => $_SERVER['SERVER_NAME'],
            'iat' => time(),
            'rol' => $rol,
            'nom' => $nombre
        ];
        return [
            "token" => JWT::encode($payload, $key, 'HS256'),
            "tkRef" => JWT::encode($payloadRef, $key, 'HS256')
        ];
    }

    public function iniciar(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody());
        try {
            if ($datos = $this->autenticar($body->usuario, $body->passw)) {
                $tokens = $this->generarToken($body->usuario, $datos['rol'], $datos['nombre']);

                $this->modificarToken($body->usuario, $tokens['tkRef']);
                $response->getBody()->write(json_encode($tokens));
                $status = 200;
            } else {
                // Credenciales inválidas
                $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
                $status = 401;
            }
        } catch (\Exception $e) {
            // Registrar el error en los logs
            error_log("Error en iniciar: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Error interno del servidor']));
            $status = 500;
        }
        return $response->withHeader('Content-type', 'application/json')
        ->withStatus($status);
    }

    public function refrescar(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $rol = $this->verificarToken($body->idUsuario, $body->tkRef);
        $status = 200;
        if($rol){
            $datos = JWT::decode($body->tkRef, new Key($this->container->get('key'), 'HS256'));
            $tokens = $this->generarToken($body->idUsuario, $datos->rol, $datos->nom);
            $this->modificarToken(idUsuario :$body->idUsuario, tkRef: $tokens['tkRef']);
            $response->getBody()->write(json_encode($tokens));
            // guardamos el token de refresco
        }else {
            $status = 401;
        }
        return $response->withHeader('Content-type', 'Application/json')->withStatus($status);
    }
}
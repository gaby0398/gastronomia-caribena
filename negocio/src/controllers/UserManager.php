<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\controllers\ServicioCURL;

class UserManager extends ServicioCURL
{
    private const ENDPOINT = "/usuario";

     /**
     * Subir foto de perfil.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function subirFoto(Request $request, Response $response): Response
    {
        // Verificar si se recibió un archivo
        if (!isset($_FILES['foto'])) {
            $response->getBody()->write(json_encode(['error' => 'No se recibió ninguna foto.']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $foto = $_FILES['foto'];

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($foto['type'], $allowedTypes)) {
            $response->getBody()->write(json_encode(['error' => 'Formato de archivo no permitido.']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Preparar los datos para enviar con CURL
        $filePath = $foto['tmp_name'];
        $fileName = $foto['name'];
        $fileData = curl_file_create($filePath, $foto['type'], $fileName);

        $formData = ['foto' => $fileData];

        // Llamar al endpoint del backend
        $endpoint = self::ENDPOINT . '/subirFoto';
        $respA = $this->ejecutarCURL($endpoint, 'POST', $formData, true);

        // Retornar la respuesta del servidor
        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function createUser(Request $request, Response $response, array $args): Response
    {
        $datos = $request->getBody();
        $respA = $this->ejecutarCURL(self::ENDPOINT, 'POST', $datos);

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Obtener usuarios o un usuario específico.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUsers(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $endpoint = self::ENDPOINT;

        if ($id !== null) {
            $endpoint .= "/$id";
        }

        $respA = $this->ejecutarCURL($endpoint, 'GET');

        if ($respA['status'] === 204) {
            return $response->withStatus(204);
        }

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Filtrar usuarios por ciertos criterios.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function filterUsers(Request $request, Response $response, array $args): Response
    {
        $params = $request->getQueryParams();
        $queryString = http_build_query($params);
        $endpoint = self::ENDPOINT . '/filtro?' . $queryString;

        $respA = $this->ejecutarCURL($endpoint, 'GET');

        if ($respA['status'] === 204) {
            return $response->withStatus(204);
        }

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Actualizar información de un usuario específico.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $datos = $request->getBody();
        $endpoint = self::ENDPOINT . "/$id";

        $respA = $this->ejecutarCURL($endpoint, 'PATCH', $datos);

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Eliminar un usuario específico.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $endpoint = self::ENDPOINT . "/$id";

        $respA = $this->ejecutarCURL($endpoint, 'DELETE');

        if ($respA['status'] === 200) {
            $response->getBody()->write($respA['resp']);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } elseif ($respA['status'] === 204) {
            return $response->withStatus(204);
        } else {
            $response->getBody()->write($respA['resp']);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($respA['status']);
        }
    }

    /**
     * Cambiar el rol de un usuario.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function cambiarRol(Request $request, Response $response, array $args): Response
    {
        $aliasOrCorreo = $args['aliasOrCorreo'];
        $body = $request->getBody();

        $endpoint = self::ENDPOINT . "/cambiarRol/$aliasOrCorreo";
        $respA = $this->ejecutarCURL($endpoint, 'POST', $body);

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Cambiar la contraseña de un usuario.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function cambiarPassw(Request $request, Response $response, array $args): Response
    {
        $aliasOrCorreo = $args['aliasOrCorreo'];
        $body = $request->getBody();

        $endpoint = self::ENDPOINT . "/$aliasOrCorreo/cambiarPassw";
        $respA = $this->ejecutarCURL($endpoint, 'POST', $body);

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Resetear la contraseña de un usuario.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function resetearPassw(Request $request, Response $response, array $args): Response
    {
        $aliasOrCorreo = $args['aliasOrCorreo'];
        $endpoint = self::ENDPOINT . "/$aliasOrCorreo/resetearPassw";

        $respA = $this->ejecutarCURL($endpoint, 'POST');

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }

    /**
     * Obtener información de un usuario específico.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUser(Request $request, Response $response, array $args): Response
    {
        $userParam = $args['userParam'] ?? null;

        if (!$userParam) {
            $error = ["error" => "Parámetro userParam no proporcionado."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $endpoint = self::ENDPOINT . "/$userParam";
        $respA = $this->ejecutarCURL($endpoint, 'GET');

        if ($respA['status'] === 404) {
            $error = ["error" => "Usuario no encontrado."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $response->getBody()->write($respA['resp']);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($respA['status']);
    }
}
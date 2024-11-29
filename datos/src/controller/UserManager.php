<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class UserManager extends Autenticar
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    /**
     * Subir foto de perfil.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function subirFoto(Request $request, Response $response): Response
    {
        // Ruta de la carpeta donde guardar las fotos
        $uploadDir = __DIR__ . '/../../assets/'; // Ajusta la ruta según tu estructura

        // Verificar si se recibió un archivo
        if (isset($_FILES['foto'])) {
            $foto = $_FILES['foto'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            // Validar tipo de archivo
            if (!in_array($foto['type'], $allowedTypes)) {
                $response->getBody()->write(json_encode(['error' => 'Formato de archivo no permitido.']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }

            // Generar un nombre único para el archivo
            $fileName = uniqid() . '-' . basename($foto['name']);
            $uploadFile = $uploadDir . $fileName;

            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($foto['tmp_name'], $uploadFile)) {
                // Obtener el alias del usuario actual desde la sesión o token (ajusta según tu lógica)
                $alias = $this->obtenerUsuarioActual();

                if (!$alias) {
                    $response->getBody()->write(json_encode(['error' => 'Usuario no autenticado.']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(401);
                }

                // Actualizar la URL de la foto en la base de datos
                $con = $this->container->get('bd');
                $sql = "UPDATE usuario SET foto = :foto WHERE alias = :alias";

                try {
                    $stmt = $con->prepare($sql);
                    $stmt->bindValue(':foto', 'assets/' . $fileName, PDO::PARAM_STR);
                    $stmt->bindValue(':alias', $alias, PDO::PARAM_STR);
                    $stmt->execute();

                    $response->getBody()->write(json_encode([
                        'url' => 'assets/' . $fileName,
                        'message' => 'Foto subida correctamente.'
                    ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                } catch (\PDOException $e) {
                    error_log("Error al actualizar la foto en la base de datos: " . $e->getMessage());
                    $response->getBody()->write(json_encode(['error' => 'Error interno del servidor.']));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(500);
                }
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error al guardar el archivo.']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }
        } else {
            $response->getBody()->write(json_encode(['error' => 'No se recibió ninguna foto.']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }

    /**
     * Obtener el alias del usuario autenticado.
     *
     * @return string|null
     */
    private function obtenerUsuarioActual(): ?string
    {
        // Ajusta esto según tu implementación de autenticación
        // Ejemplo: Obtener alias del token o de la sesión
        return $_SESSION['alias'] ?? null;
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
        $datos = $request->getParsedBody();

        // Depuración: Registrar los datos recibidos
        error_log("Datos recibidos: " . print_r($datos, true));

        // Verificar que $datos es un array
        if (!is_array($datos)) {
            $error = ["error" => "Datos de entrada inválidos."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Validar que todos los campos necesarios estén presentes
        $requiredFields = ['alias', 'nombre', 'apellido1', 'apellido2', 'telefono', 'celular', 'correo', 'rol', 'passw'];
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field]) || trim($datos[$field]) === '') {
                $error = ["error" => "El campo '$field' es requerido."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
        }

        // Obtener la conexión a la base de datos
        $con = $this->container->get('bd');

        // Verificar si el alias ya existe
        $stmt = $con->prepare("SELECT COUNT(*) FROM usuario WHERE alias = :alias");
        $stmt->bindValue(':alias', $datos['alias'], PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $error = ["error" => "El alias ya está en uso."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        }

        // Verificar si el correo ya existe
        $stmt = $con->prepare("SELECT COUNT(*) FROM usuario WHERE correo = :correo");
        $stmt->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            $error = ["error" => "El correo ya está en uso."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409);
        }

        // Iniciar la transacción
        $con->beginTransaction();

        try {
            $sql = "INSERT INTO usuario (alias, nombre, apellido1, apellido2, telefono, celular, correo, rol, passw) 
                VALUES (:alias, :nombre, :apellido1, :apellido2, :telefono, :celular, :correo, :rol, :passw)";
            $stmt = $con->prepare($sql);

            // Hash de la contraseña
            $hashedPassword = password_hash($datos['passw'], PASSWORD_BCRYPT, ['cost' => 10]);

            // Vinculación de parámetros
            $stmt->bindValue(':alias', $datos['alias'], PDO::PARAM_STR);
            $stmt->bindValue(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindValue(':apellido1', $datos['apellido1'], PDO::PARAM_STR);
            $stmt->bindValue(':apellido2', $datos['apellido2'], PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindValue(':celular', $datos['celular'], PDO::PARAM_STR);
            $stmt->bindValue(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindValue(':rol', $datos['rol'], PDO::PARAM_INT);
            $stmt->bindValue(':passw', $hashedPassword, PDO::PARAM_STR);

            $stmt->execute();
            $con->commit();

            $message = ["message" => "Usuario creado exitosamente."];
            $response->getBody()->write(json_encode($message));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (\PDOException $e) {
            $con->rollBack();
            // Registrar el error
            error_log("Error al crear usuario: " . $e->getMessage());

            // Manejo genérico de errores
            $error = ["error" => "Error interno del servidor."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    /**
     * Obtener información de un usuario.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUser(Request $request, Response $response, array $args): Response
    {
        $param = $args['userParam'] ?? null;

        if (!$param) {
            $error = ["error" => "Parámetro userParam no proporcionado."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $con = $this->container->get('bd');

        try {
            $sql = "CALL getUser(:userParam)";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':userParam', $param, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $response->getBody()->write(json_encode($result));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $error = ["error" => "Usuario no encontrado."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        } catch (\PDOException $e) {
            error_log("Error en getUser: " . $e->getMessage());
            $error = ["error" => "Error en la base de datos."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    /**
     * Obtener usuarios.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getUsers(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $con = $this->container->get('bd');

        try {
            if ($id !== null) {
                // Uso del procedimiento almacenado getUser
                $sql = "CALL getUser(:userParam)";
                $stmt = $con->prepare($sql);
                $stmt->bindValue(':userParam', $id, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $response->getBody()->write(json_encode($result));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                } else {
                    $error = ["error" => "Usuario no encontrado."];
                    $response->getBody()->write(json_encode($error));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(404);
                }
            } else {
                // Obtener todos los usuarios
                $sql = "SELECT id, alias, nombre, apellido1, apellido2, celular, correo, rol FROM usuario";
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    $response->getBody()->write(json_encode($result));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
                } else {
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(204); // No Content
                }
            }
        } catch (\PDOException $e) {
            error_log("Error en getUsers: " . $e->getMessage());
            $error = ["error" => "Error en la base de datos."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
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
        $con = $this->container->get('bd');

        // Definir campos permitidos para filtrar
        $allowedFields = ['alias', 'nombre', 'apellido1', 'apellido2', 'correo'];
        $conditions = [];
        $bindParams = [];

        foreach ($params as $key => $value) {
            if (in_array($key, $allowedFields) && !empty($value)) {
                $conditions[] = "$key LIKE :$key";
                $bindParams[":$key"] = "%" . $value . "%";
            }
        }

        if (empty($conditions)) {
            $error = ["error" => "No se proporcionaron criterios de filtrado válidos."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $sql = "SELECT id, alias, correo, rol FROM usuario WHERE " . implode(' AND ', $conditions);
        try {
            $stmt = $con->prepare($sql);
            foreach ($bindParams as $param => $value) {
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0) {
                $response->getBody()->write(json_encode($result));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204); // No Content
            }
        } catch (\PDOException $e) {
            error_log("Error en filterUsers: " . $e->getMessage());
            $error = ["error" => "Error en la base de datos."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    /**
     * Actualizar información de un usuario específico (actualización parcial).
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $datos = $request->getParsedBody();

        // Validar que se proporcione al menos un campo para actualizar
        if (empty($datos)) {
            $error = ["error" => "No se proporcionaron datos para actualizar."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Actualizar el usuario
        $status = $this->updateUserInternal($datos, $id);

        // Manejar la respuesta según el estado
        switch ($status) {
            case 200:
                $message = ["message" => "Usuario actualizado exitosamente."];
                $response->getBody()->write(json_encode($message));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            case 204:
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204);
            case 400:
                $error = ["error" => "No hay campos válidos para actualizar."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            default:
                $error = ["error" => "Error interno del servidor."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
        }
    }

    /**
     * Función interna para actualizar un usuario.
     *
     * @param array $datos
     * @param int $id
     * @return int Código de estado HTTP
     */
    private function updateUserInternal(array $datos, int $id): int
    {
        $con = $this->container->get('bd');

        // Filtrar solo los campos permitidos para actualizar
        $allowedFields = ['alias', 'nombre', 'apellido1', 'apellido2', 'telefono', 'celular', 'correo', 'passw'];
        $fields = [];
        $bindParams = [];

        foreach ($datos as $key => $value) {
            if (in_array($key, $allowedFields)) {
                if ($key === 'passw') {
                    $fields[] = "$key = :$key";
                    $bindParams[":$key"] = password_hash($value, PASSWORD_BCRYPT, ['cost' => 10]);
                } else {
                    $fields[] = "$key = :$key";
                    $bindParams[":$key"] = $value;
                }
            }
        }

        if (empty($fields)) {
            // No hay campos válidos para actualizar
            return 400; // Bad Request
        }

        $sql = "UPDATE usuario SET " . implode(', ', $fields) . " WHERE id = :id";
        $bindParams[':id'] = $id;

        try {
            $stmt = $con->prepare($sql);
            foreach ($bindParams as $param => $value) {
                // Determinar el tipo de parámetro
                if ($param === ':rol' || $param === ':id') {
                    $type = PDO::PARAM_INT;
                } else {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue($param, $value, $type);
            }

            $stmt->execute();
            return $stmt->rowCount() > 0 ? 200 : 204;
        } catch (\PDOException $e) {
            error_log("Error en updateUserInternal: " . $e->getMessage());
            return 500; // Error interno del servidor
        }
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
        $id = (int)$args['id'];
        $con = $this->container->get('bd');

        try {
            $sql = "DELETE FROM usuario WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $message = ["message" => "Usuario eliminado exitosamente."];
                $response->getBody()->write(json_encode($message));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204); // No Content
            }
        } catch (\PDOException $e) {
            error_log("Error en deleteUser: " . $e->getMessage());
            $error = ["error" => "Error interno del servidor."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function cambiarPassw(Request $request, Response $response, array $args): Response
    {
        $aliasOrCorreo = $args['aliasOrCorreo'];
        $body = $request->getParsedBody();

        // Validar que se proporcionen las contraseñas
        if (empty($body['passw']) || empty($body['passwN'])) {
            $error = ["error" => "Contraseñas no proporcionadas."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Autenticar al usuario con el alias o correo y la contraseña actual
        $autenticado = $this->autenticar($aliasOrCorreo, $body['passw'], true);
        if ($autenticado) {
            // Obtener el ID del usuario basado en alias o correo
            $userId = $this->obtenerIdUsuario($aliasOrCorreo);
            if (!$userId) {
                $error = ["error" => "Usuario no encontrado."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }

            $status = $this->updateUserInternal(['passw' => $body['passwN']], $userId);
            $message = $status === 200 ? 'Contraseña actualizada.' : ($status === 204 ? 'No se realizaron cambios.' : 'Error al actualizar la contraseña.');

            $response->getBody()->write(json_encode(['message' => $message]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        } else {
            $error = ["error" => "Credenciales inválidas."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
    }

    /**
     * Método para obtener el ID del usuario basado en alias o correo.
     *
     * @param string $aliasOrCorreo
     * @return int|null
     */
    private function obtenerIdUsuario(string $aliasOrCorreo): ?int
    {
        $con = $this->container->get('bd');
        $sql = "SELECT id FROM usuario WHERE alias = :alias OR correo = :correo LIMIT 1";

        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':alias', $aliasOrCorreo, PDO::PARAM_STR);
            $stmt->bindValue(':correo', $aliasOrCorreo, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado ? (int)$resultado['id'] : null;
        } catch (\PDOException $e) {
            error_log("Error en obtenerIdUsuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Resetear la contraseña de un usuario (por ejemplo, por un administrador).
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function resetearPassw(Request $request, Response $response, array $args): Response
    {
        $idUsuario = (int)$args['aliasOrCorreo'];

        try {
            // Generar una contraseña temporal segura
            $temporaryPassword = bin2hex(random_bytes(8));

            // Actualizar la contraseña del usuario
            $status = $this->updateUserInternal(['passw' => $temporaryPassword], $idUsuario);

            if ($status === 200) {
                // Opcional: enviar la contraseña temporal al usuario por correo
                // Implementar lógica de envío de correo aquí

                $response->getBody()->write(json_encode([
                    'message' => 'Contraseña reseteada exitosamente.',
                    'passwTemp' => $temporaryPassword // Para pruebas; en producción, no se recomienda devolver la contraseña
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } elseif ($status === 204) {
                $response->getBody()->write(json_encode(['message' => 'Usuario no encontrado o no se realizaron cambios.']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(204);
            } else {
                $error = ["error" => "Error al resetear la contraseña."];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }
        } catch (\Exception $e) {
            error_log("Error en resetearPassw: " . $e->getMessage());
            $error = ["error" => "Error interno del servidor."];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function cambiarRol(Request $request, Response $response, $args)
    {
        $body = json_decode($request->getBody(), true);

        // Validar que se haya proporcionado el nuevo rol
        if (!isset($body['rol'])) {
            $response->getBody()->write(json_encode(['error' => 'El nuevo rol no fue proporcionado']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $nuevoRol = (int)$body['rol'];
        $aliasOrCorreo = $args['aliasOrCorreo'];

        try {
            $con = $this->container->get('bd');

            // Llamar al procedimiento almacenado
            $stmt = $con->prepare("CALL cambiarRolUsuario(:aliasORcorreo, :nuevoRol)");
            $stmt->bindValue(':aliasORcorreo', $aliasOrCorreo, PDO::PARAM_STR);
            $stmt->bindValue(':nuevoRol', $nuevoRol, PDO::PARAM_INT);
            $stmt->execute();

            $response->getBody()->write(json_encode(['message' => 'Rol actualizado exitosamente']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (\PDOException $e) {
            // Manejar errores específicos de SQLSTATE
            if ($e->getCode() == '45000') {
                $errorMessage = $e->getMessage();
                $status = 400; // Bad Request
            } else {
                $errorMessage = 'Error interno del servidor';
                $status = 500; // Internal Server Error
            }

            error_log("Error al cambiar rol: " . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => $errorMessage]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        }
    }


}
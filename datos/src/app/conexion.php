<?php

use Psr\Container\ContainerInterface;

$container->set('bd', function (ContainerInterface $c) {
    $conf = $c->get('config_bd');

    $opc = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false, // Para mejorar la seguridad
    ];

    // Asegúrate de que el charset está configurado correctamente
    // Es recomendable definir el charset directamente aquí para evitar inconsistencias
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$conf->host};dbname={$conf->db};charset={$charset}";

    try {
        $con = new PDO($dsn, $conf->usr, $conf->passw, $opc);
        // Opcional: Establecer la colación a nivel de conexión si es necesario
        // $con->exec("SET collation_connection = utf8mb4_0900_ai_ci");
    } catch (PDOException $e) {
        // Es una buena práctica no exponer información sensible en entornos de producción
        // Puedes registrar el error y mostrar un mensaje genérico al usuario
        error_log('Error de conexión a la base de datos: ' . $e->getMessage());
        die("Error conectando a la base de datos");
    }

    return $con;
});

<?php

use Psr\Container\ContainerInterface;

$container->set('bd',function(ContainerInterface $c){
    $conf = $c->get('config_bd');

    $opc=[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    #dsn = siglas de conexion a la bases de datos
    $dsn = "mysql:host=$conf->host;dbname=$conf->db;charset=$conf->charset";

    try {
        $con = new PDO($dsn, $conf->usr, $conf->passw, $opc);
        // die("Conecto a la base datos");
    } catch (PDOException $e) {
        print('Erro '.$e->getMessage().'</br>');
        die("Error conectando a la base de datos");
    }
    return $con;
});
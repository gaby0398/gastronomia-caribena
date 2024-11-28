<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-API-KEY, Origen, X-Request-Width, Content-Type, Accept, Access-Control-Request-Method, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH');
header('Allow: GET, POST, OPTIONS, PUT, DELETE, PATCH');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT & ~E_WARNING);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    die('Metodo no permitido');
}
    require "../src/app/app.php";    
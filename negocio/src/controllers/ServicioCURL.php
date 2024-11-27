<?php

namespace App\controllers;

class ServicioCURL
{
    //private const URL = "http://web-datos/api";
    //private const URL = "http://192.168.0.12/api";
    private const URL = "http://servidor-datosPC/api";


    public function ejecutarCURL($endpoint, $metodo, $datos = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Configurar cabeceras por defecto
        $headers = [
            'Accept: application/json',
        ];

        if ($datos != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
            // Agregar cabecera Content-Type: application/json
            $headers[] = 'Content-Type: application/json';
        }

        // Establecer las cabeceras
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        switch ($metodo) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo); // PUT, PATCH o DELETE
                break;
        }

        // Ejecutar la solicitud y manejar errores
        $resp = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            // Manejar el error de cURL
            return ['resp' => json_encode(['error' => $error]), 'status' => 500];
        }

        return ['resp' => $resp, 'status' => $status];
    }
}
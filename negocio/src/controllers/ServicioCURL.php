<?php

namespace App\controllers;

class ServicioCURL
{
    private const URL = "http://web-datos/api";

    public function ejecutarCURL($endpoint, $metodo, $datos = null)
    {
        //Esto lo habilita para todos
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //Esto es porque tanto el POST como el PUT almacenan datos
        if ($datos != null) { //Si hay datos
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos); //Habilita el POST
        }

        switch ($metodo) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo); //PUT, PATCH o DELETE
                break;
        }

        $resp = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['resp' => $resp, 'status' => $status];
    }
}

<?php

function obtenerConexion() {
    static $conectar = null;

    if ($conectar !== null) {
        return $conectar;
    }

    $host     = "127.0.0.1";
    $usuario  = "root";
    $password = ""; // Como entraste sin clave a DataGrip, esto se queda vacío
    $database = "homecare_dialysis";

    // Conexión con MySQLi orientado a objetos
    $conectar = new mysqli($host, $usuario, $password, $database);

    if ($conectar->connect_error) {
        die("Error de conexión: " . $conectar->connect_error);
    }

    return $conectar;
}
?>
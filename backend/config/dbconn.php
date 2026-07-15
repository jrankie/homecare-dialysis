<?php

$host     = "127.0.0.1";
$usuario  = "root";
$password = "root"; // sin clave en datagrip
$database = "homecare_dialysis";
$conectar = mysqli_connect($host, $usuario, $password, $database);
if (!$conectar) {
    die("No se pudo conectar a la base de datos: ". mysqli_connect_error());
}
?>


<?php

$host     = "127.0.0.1";
$usuario  = "root";
$password = ""; // Como entraste sin clave a DataGrip, esto se queda vacío
$database = "homecare_dialysis";
$conectar = mysqli_connect($host, $usuario, $password, $database);
if (!$conectar) {
    die("No se pudo conectar a la base de datos: ". mysqli_connect_error());
} else {
    echo '<script language="javascript">alert("Conexión exitosa!");</script>';
}
?>


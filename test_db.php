<?php
require_once 'backend/config/dbconn.php';

try {
    $db = obtenerConexion();
    echo "<h1>Base de datos conectada exitosamente</h1>";
    echo "PHP se conectó correctamente a MySQL local.<br><br>";

    // Consulta estilo MySQLi
    $resultado = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $fila = $resultado->fetch_assoc();

    echo "Número de usuarios registrados en la base de datos: <strong>" . $fila['total'] . "</strong>";
} catch (Exception $e) {
    echo "<h1>ERROR</h1>";
    echo "Error: " . $e->getMessage();
}
?>
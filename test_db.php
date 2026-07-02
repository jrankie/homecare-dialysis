<?php
//Este es un archivo para probar la conexion con la base de datos

// Incluimos tu motor de conexión
require_once 'backend/config/database.php';

try {
    // Intentamos abrir la conexión
    $db = obtenerConexion();
    echo "<h1>Base de datos conectada exitosamente</h1>";
    echo "PHP se conectó correctamente a Supabase.<br><br>";

    // Hacemos una consulta de prueba
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $resultado = $stmt->fetch();

    echo "Número de usuarios registrados en la base de datos: <strong>" . $resultado['total'] . "</strong>";
} catch (Exception $e) {
    echo "<h1>ERROR</h1>";
    echo "Error: " . $e->getMessage();
}
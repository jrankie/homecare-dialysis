<?php

/**
 * Carga las variables de entorno desde el archivo .env de la raíz
 */
function cargarEnv($ruta) {
    if (!file_exists($ruta)) {
        return;
    }
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0) continue; // Ignorar comentarios
        list($nombre, $valor) = explode('=', $linea, 2);
        $_ENV[trim($nombre)] = trim($valor);
    }
}

/**
 * Retorna una instancia de conexión PDO vinculada a Supabase
 */
function obtenerConexion() {
    static $pdo = null;

    // Si ya existe la conexión, la reutilizamos
    if ($pdo !== null) {
        return $pdo;
    }

    // Cargamos el .env subiendo dos niveles desde backend/config/ hacia la raíz
    cargarEnv(__DIR__ . '/../../.env');

    $host     = $_ENV['DB_HOST'] ?? null;
    $port     = $_ENV['DB_PORT'] ?? null;
    $dbname   = $_ENV['DB_NAME'] ?? null;
    $user     = $_ENV['DB_USER'] ?? null;
    $password = $_ENV['DB_PASSWORD'] ?? null;

    // Data Source Name (DSN) para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $password, $opciones);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión con la base de datos: " . $e->getMessage());
    }
}
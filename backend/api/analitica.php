<?php
session_start();
require_once '../config/database.php';

// 1. Validar que haya una sesión activa
if (!isset($_SESSION['usuario'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(["error" => "No autorizado. Inicie sesión."]);
    exit();
}

$db = obtenerConexion();

// Determinar el paciente_id a consultar
$paciente_id = null;

if ($_SESSION['rol'] === 'paciente') {
    // Si es paciente, solo puede ver sus propios datos
    $paciente_id = $_SESSION['paciente_id'] ?? null;
} else {
    // Si es admin o médico, puede ver los datos de cualquier paciente que se reciba por parámetro
    $paciente_id = isset($_REQUEST['paciente_id']) ? intval($_REQUEST['paciente_id']) : null;
}

if (!$paciente_id) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "ID de paciente no especificado o perfil no encontrado."]);
    mysqli_close($db);
    exit();
}

// Obtener la fecha seleccionada
$fecha = isset($_REQUEST['fecha']) ? trim($_REQUEST['fecha']) : '';

if (empty($fecha)) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Fecha no especificada."]);
    mysqli_close($db);
    exit();
}

// 2. Consultar los 4 recambios de la fecha ordenada por número de recambio
$sql = $db->prepare("SELECT recambio_num, concentracion, infusion, drenaje, cualidad, balance 
                     FROM recambios 
                     WHERE paciente_id = ? AND fecha_tratamiento = ? 
                     ORDER BY recambio_num ASC");
$sql->bind_param('is', $paciente_id, $fecha);
$sql->execute();
$resultado = $sql->get_result();

$recambios = [];
while ($fila = $resultado->fetch_assoc()) {
    $recambios[] = [
        "recambio_num" => intval($fila['recambio_num']),
        "concentracion" => $fila['concentracion'],
        "infusion" => intval($fila['infusion']),
        "drenaje" => intval($fila['drenaje']),
        "cualidad" => $fila['cualidad'],
        "balance" => intval($fila['balance'])
    ];
}

$sql->close();
mysqli_close($db);

// 3. Retornar los datos en formato JSON de manera limpia
header('Content-Type: application/json');
echo json_encode($recambios);
?>

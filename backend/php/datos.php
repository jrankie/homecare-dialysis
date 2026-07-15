<?php
session_start();
require '../config/dbconn.php';

header('Content-Type: application/json');

$paciente_id = $_SESSION['paciente_id'];
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sql = $conectar->prepare("SELECT recambio_num, infusion, drenaje, balance FROM recambios WHERE paciente_id = ? AND fecha_tratamiento = ? ORDER BY recambio_num ASC");
$sql->bind_param('is', $paciente_id, $fecha);
$sql->execute();
$resultado = $sql->get_result();

$datos = array();
while ($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

$sql->close();
mysqli_close($conectar);

echo json_encode($datos);
?>
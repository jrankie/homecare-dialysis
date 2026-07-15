<?php
session_start();
require '../config/dbconn.php';

header('Content-Type: application/json');

$paciente_id = $_SESSION['paciente_id'];
$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$fin = isset($_GET['fin']) ? $_GET['fin'] : '';

$sql = $conectar->prepare("SELECT valor_glucosa, momento, created_at FROM glicemias WHERE paciente_id = ? AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at ASC");
$sql->bind_param('iss', $paciente_id, $inicio, $fin);
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

<?php
session_start();
require '../config/dbconn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $sql = $conectar->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $sql->bind_param('s', $usuario);
    $sql->execute();
    $resultado = $sql->get_result();

    if (($fila = $resultado->fetch_assoc()) && password_verify($contrasena, $fila['contrasena'])) {

        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol'] = $fila['rol'];
        $_SESSION['usuario_id'] = $fila['id'];
        $_SESSION['nombre'] = $fila['nombre'];

        if ($fila['rol'] == 'paciente') {
            $sql2 = $conectar->prepare("SELECT id FROM pacientes WHERE usuario_id = ?");
            $sql2->bind_param('i', $fila['id']);
            $sql2->execute();
            $res2 = $sql2->get_result();
            if ($fila2 = $res2->fetch_assoc()) {
                $_SESSION['paciente_id'] = $fila2['id'];
            }
            $sql2->close();
            header("Location: ../../frontend/pages/balance.php");
            exit();
        } else {
            header("Location: ../../frontend/pages/general.php");
            exit();
        }

    } else {
        $error = "Usuario o contraseña incorrectos.";
    }

    $sql->close();
}
?>
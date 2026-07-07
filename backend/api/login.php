<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $conectar = obtenerConexion();

    // Buscar usuario
    $sql = $conectar->prepare("SELECT id, nombre, usuario, contrasena, rol FROM usuarios WHERE usuario = ?");
    $sql->bind_param('s', $usuario);
    $sql->execute();
    $resultado = $sql->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        // Verificar contraseña
        if (password_verify($contrasena, $fila['contrasena'])) {
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['nombre'] = $fila['nombre'];

            // Obtener paciente_id correspondiente si es paciente
            if ($fila['rol'] == 'paciente') {
                $sql2 = $conectar->prepare("SELECT id FROM pacientes WHERE usuario_id = ?");
                $sql2->bind_param('i', $fila['id']);
                $sql2->execute();
                $res2 = $sql2->get_result();
                if ($fila2 = $res2->fetch_assoc()) {
                    $_SESSION['paciente_id'] = $fila2['id'];
                }
                $sql2->close();
                header("Location: ../../frontend/pages/balance.html");
                exit();
            } else {
                header("Location: ../../frontend/pages/index.html");
                exit();
            }
        }
    }

    $mensaje = "Usuario o contraseña incorrectos.";
    echo "<script language='javascript'>
        alert('$mensaje'); 
        window.location.href = '../../frontend/pages/login.html';
    </script>";
    
    $sql->close();
    mysqli_close($conectar);
}
?>

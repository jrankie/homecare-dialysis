<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
        $usuario = trim($_POST['usuario']);
        $contrasena = trim($_POST['contrasena']);

        $db = obtenerConexion();

        // 1. Buscar el usuario en la base de datos
        $sql = $db->prepare("SELECT id, nombre, usuario, contrasena, rol FROM usuarios WHERE usuario = ?");
        $sql->bind_param('s', $usuario);
        $sql->execute();
        $resultado = $sql->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();

            // 2. Verificar contraseña hasheada
            if (password_verify($contrasena, $fila['contrasena'])) {
                // Guardar datos básicos en la sesión
                $_SESSION['usuario_id'] = $fila['id'];
                $_SESSION['nombre'] = $fila['nombre'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['rol'] = $fila['rol'];

                // 3. Si es paciente, obtener su paciente_id correspondiente
                if ($fila['rol'] === 'paciente') {
                    $sql_paciente = $db->prepare("SELECT id FROM pacientes WHERE usuario_id = ?");
                    $sql_paciente->bind_param('i', $fila['id']);
                    $sql_paciente->execute();
                    $res_paciente = $sql_paciente->get_result();
                    if ($res_paciente->num_rows === 1) {
                        $fila_paciente = $res_paciente->fetch_assoc();
                        $_SESSION['paciente_id'] = $fila_paciente['id'];
                    }
                    $sql_paciente->close();

                    // Redirigir a la pantalla de Balance Hídrico
                    header("Location: ../../frontend/pages/balance.html");
                    exit();
                } 
                // 4. Si es médico, obtener su medico_id
                elseif ($fila['rol'] === 'medico') {
                    $sql_medico = $db->prepare("SELECT id FROM medicos WHERE usuario_id = ?");
                    $sql_medico->bind_param('i', $fila['id']);
                    $sql_medico->execute();
                    $res_medico = $sql_medico->get_result();
                    if ($res_medico->num_rows === 1) {
                        $fila_medico = $res_medico->fetch_assoc();
                        $_SESSION['medico_id'] = $fila_medico['id'];
                    }
                    $sql_medico->close();

                    // Redirigir a la pantalla de reportes
                    header("Location: ../../frontend/pages/reportes.html");
                    exit();
                } 
                // 5. Si es administrador
                else {
                    header("Location: ../../frontend/pages/index.html");
                    exit();
                }
            }
        }

        // Si falla la autenticación
        $mensaje = "Usuario o contraseña incorrectos.";
        echo "<script language='javascript'>
            alert('$mensaje');
            window.location.href = '../../frontend/pages/login.html';
        </script>";
        $sql->close();
        mysqli_close($db);
    }
}
?>

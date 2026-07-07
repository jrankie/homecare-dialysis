<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nombre']) && isset($_POST['usuario']) && isset($_POST['contrasena'])) {
        $nombre = trim($_POST['nombre']);
        $usuario = trim($_POST['usuario']);
        $contrasena = trim($_POST['contrasena']);

        if (empty($nombre) || empty($usuario) || empty($contrasena)) {
            echo "<script language='javascript'>
                alert('Todos los campos son obligatorios.');
                window.history.back();
            </script>";
            exit();
        }

        $db = obtenerConexion();

        // Verificar si el usuario ya existe
        $check_sql = $db->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $check_sql->bind_param('s', $usuario);
        $check_sql->execute();
        $check_res = $check_sql->get_result();

        if ($check_res->num_rows > 0) {
            echo "<script language='javascript'>
                alert('El nombre de usuario ya está registrado. Intente con otro.');
                window.history.back();
            </script>";
            $check_sql->close();
            mysqli_close($db);
            exit();
        }
        $check_sql->close();

        // Iniciar transacción para asegurar que ambas inserciones tengan éxito
        $db->begin_transaction();

        try {
            // Hashear contraseña
            $hash_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

            // 1. Insertar en la tabla usuarios
            $sql_user = $db->prepare("INSERT INTO usuarios (nombre, usuario, contrasena, rol) VALUES (?, ?, ?, 'paciente')");
            $sql_user->bind_param('sss', $nombre, $usuario, $hash_contrasena);
            $sql_user->execute();
            $usuario_id = $db->insert_id;
            $sql_user->close();

            // 2. Insertar en la tabla pacientes
            $sql_paciente = $db->prepare("INSERT INTO pacientes (usuario_id, nombre_completo) VALUES (?, ?)");
            $sql_paciente->bind_param('is', $usuario_id, $nombre);
            $sql_paciente->execute();
            $sql_paciente->close();

            // Confirmar transacción
            $db->commit();

            $mensaje = "Paciente registrado exitosamente. Ya puede iniciar sesión.";
            echo "<script language='javascript'>
                alert('$mensaje');
                window.location.href = '../../frontend/pages/login.html';
            </script>";

        } catch (Exception $e) {
            // Deshacer cambios en caso de error
            $db->rollback();
            $mensaje = "Error al registrar el paciente: " . $e->getMessage();
            echo "<script language='javascript'>
                alert('$mensaje');
                window.history.back();
            </script>";
        }

        mysqli_close($db);
    }
}
?>

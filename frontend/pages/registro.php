<?php
session_start();

require_once "../../backend/config/dbconn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $check_sql = $conectar->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $check_sql->bind_param('s', $usuario);
    $check_sql->execute();
    $check_res = $check_sql->get_result();

    if ($check_res->num_rows > 0) {
        $mensaje = "El nombre de usuario ya está registrado.";
        echo "<script language='javascript'>alert('$mensaje'); window.history.back();</script>";
        $check_sql->close();
        mysqli_close($conectar);
        exit();
    }
    $check_sql->close();

    $hash_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

    $sql_user = $conectar->prepare("INSERT INTO usuarios (nombre, usuario, contrasena, rol) VALUES (?, ?, ?, 'paciente')");
    $sql_user->bind_param('sss', $nombre, $usuario, $hash_contrasena);

    if ($sql_user->execute()) {
        $usuario_id = mysqli_insert_id($conectar);
        
        $sql_paciente = $conectar->prepare("INSERT INTO pacientes (usuario_id, nombre_completo) VALUES (?, ?)");
        $sql_paciente->bind_param('is', $usuario_id, $nombre);
        $sql_paciente->execute();
        $sql_paciente->close();

        $mensaje = "Paciente registrado correctamente.";
        echo "<script language='javascript'>alert('$mensaje'); window.location.href = 'balance.php';</script>";
    } else {
        $mensaje = "Se ha producido un error al guardar el registro.";
        echo "<script language='javascript'>alert('$mensaje'); window.history.back();</script>";
    }

    $sql_user->close();
    mysqli_close($conectar);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Paciente - HomeCare Dialysis</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="formulario">
        <h1> Registro de Pacientes </h1> 
        <form method="post" action="registro.php">

            <div class="nombre">
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Perez" required >
                <label> Nombre y Apellido </label> 
            </div>

            <div class="username">
                <input type="text" id="usuario" name="usuario" placeholder="Ej. juanpere05" required>
                <label> Nombre de usuario</label>
            </div>

            <div class="Contrasena">
                <input type="password" id="contrasena" name="contrasena" placeholder="Ingrese su contraseña " required>
                <label> Contraseña </label>
            </div>
            
            <input type="submit" value="Registrarse">
            
            <div class="registrarse">
                <a href="javascript:history.back()">Volver</a>
            </div>


        </form>
    </div>
</body>
</html>

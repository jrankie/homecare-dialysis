<?php
session_start();
require_once '../config/database.php';

// 1. Validar que el usuario haya iniciado sesión y sea un paciente
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    echo "<script language='javascript'>
        alert('Acceso denegado. Debe iniciar sesión como paciente.');
        window.location.href = '../../frontend/pages/login.html';
    </script>";
    exit();
}

$paciente_id = $_SESSION['paciente_id'] ?? null;

if (!$paciente_id) {
    echo "<script language='javascript'>
        alert('Error: No se encontró el perfil de paciente asociado a su cuenta.');
        window.location.href = '../../frontend/pages/login.html';
    </script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar que los datos principales existan
    if (isset($_POST['fecha_tratamiento']) && isset($_POST['sistema'])) {
        $fecha_tratamiento = trim($_POST['fecha_tratamiento']);
        $sistema = trim($_POST['sistema']);
        $presion_arterial = isset($_POST['presion_arterial']) ? trim($_POST['presion_arterial']) : '';
        $pulso = isset($_POST['pulso']) && $_POST['pulso'] !== '' ? intval($_POST['pulso']) : null;

        if (empty($fecha_tratamiento) || empty($sistema)) {
            echo "<script language='javascript'>
                alert('La fecha y el sistema son obligatorios.');
                window.history.back();
            </script>";
            exit();
        }

        $db = obtenerConexion();

        // Iniciar transacción para guardar los 4 recambios juntos
        $db->begin_transaction();

        try {
            // Sentencia preparada para insertar cada recambio
            $sql = $db->prepare("INSERT INTO recambios 
                (paciente_id, fecha_tratamiento, tipo_sistemadp, presion_arterial, pulso, recambio_num, concentracion, infusion, drenaje, cualidad, balance) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 2000, ?, ?, ?)");

            // Recorrer los 4 recambios
            for ($i = 1; $i <= 4; $i++) {
                $concentracion = isset($_POST['concentracion' . $i]) ? trim($_POST['concentracion' . $i]) : '1.5%';
                $drenaje = isset($_POST['drenar' . $i]) && $_POST['drenar' . $i] !== '' ? intval($_POST['drenar' . $i]) : 0;
                $cualidad = isset($_POST['cualidad' . $i]) ? trim($_POST['cualidad' . $i]) : 'Claro';
                
                // Calcular balance: infusión (2000) - drenaje
                $balance = 2000 - $drenaje;

                // Vincular parámetros: paciente_id(i), fecha(s), sistema(s), presion(s), pulso(i), recambio_num(i), concentracion(s), drenaje(i), cualidad(s), balance(i)
                $sql->bind_param('isssiisisi', 
                    $paciente_id, 
                    $fecha_tratamiento, 
                    $sistema, 
                    $presion_arterial, 
                    $pulso, 
                    $i, 
                    $concentracion, 
                    $drenaje, 
                    $cualidad, 
                    $balance
                );

                $sql->execute();
            }

            $db->commit();
            $mensaje = "Reporte de balance hídrico guardado correctamente en la base de datos.";
            
            echo "<script language='javascript'>
                alert('$mensaje');
                window.location.href = '../../frontend/pages/balance.html';
            </script>";

        } catch (Exception $e) {
            $db->rollback();
            $mensaje = "Error al guardar el reporte: " . $e->getMessage();
            echo "<script language='javascript'>
                alert('$mensaje');
                window.history.back();
            </script>";
        }

        if (isset($sql)) {
            $sql->close();
        }
        mysqli_close($db);
    }
}
?>

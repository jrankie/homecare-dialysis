<?php
session_start();
require '../config/dbconn.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    echo "<script language='javascript'>
        alert('Inicie sesión como paciente.');
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

function obtenerDiagnostico($valor, $momento) {
    if ($valor < 70) {
        return "Hipoglucemia";
    }

    if ($momento === 'ayunas') {
        if ($valor <= 99) return "Normal";
        if ($valor <= 125) return "Prediabetes";
        return "Hiperglucemia";
    } elseif ($momento === 'antes') {
        if ($valor <= 130) return "Normal";
        if ($valor <= 140) return "Prediabetes";
        return "Hiperglucemia";
    } else {
        if ($valor < 140) return "Normal";
        if ($valor <= 199) return "Prediabetes";
        return "Hiperglucemia";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['glucosa']) && isset($_POST['momento'])) {
        $valor_glucosa = floatval($_POST['glucosa']);
        $momento = trim($_POST['momento']);

        if ($valor_glucosa <= 0 || empty($momento)) {
            echo "<script language='javascript'>
                alert('Ingrese un valor de glucosa válido.');
                window.history.back();
            </script>";
            exit();
        }

        $diagnostico = obtenerDiagnostico($valor_glucosa, $momento);


        $sql = $conectar->prepare("INSERT INTO glicemias (paciente_id, valor_glucosa, momento, diagnostico) VALUES (?, ?, ?, ?)");

        $sql->bind_param('idss', $paciente_id, $valor_glucosa, $momento, $diagnostico);

        if ($sql->execute()) {
            $mensaje = "Glicemia guardada exitosamente. Diagnóstico: " . $diagnostico;
        } else {
            $mensaje = "Se ha producido un error al guardar el registro.";
        }

        echo "<script language='javascript'>
            alert('$mensaje');
            window.location.href = '../../frontend/pages/glicemias.php';
        </script>";

        $sql->close();
        mysqli_close($conectar);
    }
}
?>
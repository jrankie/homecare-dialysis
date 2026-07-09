<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

require "../../backend/config/dbconn.php";

$rol = $_SESSION['rol'];
$nombre_usuario = $_SESSION['nombre'];
$paciente_id = $_SESSION['paciente_id'] ?? null;

$glicemia_hoy = null;
$balance_hoy = null;

if ($rol === 'paciente' && $paciente_id) {
    $sql_glicemia = $conectar->prepare("SELECT valor_glucosa, momento, diagnostico, created_at 
                                        FROM glicemias 
                                        WHERE paciente_id = ? AND DATE(created_at) = CURDATE() 
                                        ORDER BY created_at DESC LIMIT 1");
    $sql_glicemia->bind_param("i", $paciente_id);
    $sql_glicemia->execute();
    $res_glicemia = $sql_glicemia->get_result();
    $glicemia_hoy = $res_glicemia->fetch_assoc();
    $sql_glicemia->close();

    $sql_balance = $conectar->prepare("SELECT SUM(infusion) as total_infusion, SUM(drenaje) as total_drenaje, SUM(balance) as total_balance 
                                       FROM recambios 
                                       WHERE paciente_id = ? AND fecha_tratamiento = CURDATE()");
    $sql_balance->bind_param("i", $paciente_id);
    $sql_balance->execute();
    $res_balance = $sql_balance->get_result();
    $balance_hoy = $res_balance->fetch_assoc();
    $sql_balance->close();

}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel General - HomeCare Dialysis</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="login.html">Inicio de Sesion</a></li>
            <li><a href="general.php" class="activo">Panel General</a></li>
            <li><a href="balance.php">Balance Hídrico</a></li>
            <li><a href="glicemias.php">Análisis Glicemia</a></li>
            <li><a href="reportes.php">Analítica Visual</a></li>
            <li><a href="../../backend/php/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

<div class="contenido">
        <div class="panel-general">

            <div class="panel-texto">    
                <h2>Panel de Control General</h2>
                <p>Bienvenido de nuevo, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>. Rol: <em><?php echo ucfirst($rol); ?></em></p>

                <?php if ($rol === 'paciente') { ?>
                    <h3>Resumen del Día</h3>
                    
                    <p class="card"><strong>Última Glicemia de Hoy:</strong> 
                    <?php if ($glicemia_hoy) { 
                        $val = htmlspecialchars($glicemia_hoy['valor_glucosa']);
                        $mom = htmlspecialchars(ucfirst($glicemia_hoy['momento']));
                        $diag = htmlspecialchars($glicemia_hoy['diagnostico']);
                        echo "$val mg/dL ($mom) - Estado: $diag";
                    } else { 
                        echo "Sin mediciones hoy";
                    } ?>
                    </p>

                    <p class="card"><strong>Balance Hídrico de Hoy:</strong> 
                    <?php if ($balance_hoy && $balance_hoy['total_infusion'] > 0) { 
                        $inf = intval($balance_hoy['total_infusion']);
                        $dre = intval($balance_hoy['total_drenaje']);
                        $bal = intval($balance_hoy['total_balance']);
                        
                        if ($bal <= 0) {
                            $diag_bal = "Favorable";
                        } elseif ($bal <= 2000) {
                            $diag_bal = "Retención moderada";
                        } else {
                            $diag_bal = "Excesiva retención";
                        }
                        echo "$bal ml (Infundido: $inf ml, Drenado: $dre ml) - Estado: $diag_bal";
                    } else { 
                        echo "Sin registros hoy";
                    } ?>
                    </p>

                <?php } else { ?>
                    <p>Estás conectado con un perfil del personal médico.</p>
                    <p>Para consultar el estado clínico de los pacientes o ver sus gráficos de evolución, dirígete a la sección de analítica.</p>
                    <p><a href="reportes.php">Ir a Analítica Visual</a></p>
                <?php } ?>
            </div>

            <div class="panel-imagen">
                <img src="../imgs/ilustracion.png" alt="Monitoreo de salud">
            </div>

        </div>
    </div>


</body>
</html>

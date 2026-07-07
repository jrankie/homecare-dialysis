<?php
session_start();

// 1. Validar si esta la sesion iniciada, si no esta iniciada, el usuario lo regresa a login.html
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.html");
    exit();
}

require_once "../../backend/config/dbconn.php";
$paciente_id = $_SESSION['paciente_id'];

// 2. Consulta SQL para el Historial de Glicemias

$sql = $conectar->prepare("SELECT valor_glucosa, momento, diagnostico, created_at FROM glicemias WHERE paciente_id = ? ORDER BY created_at DESC");
$sql->bind_param("i", $paciente_id);
$sql->execute();
$resultado = $sql->get_result();

$historial = array();
while ($fila = $resultado->fetch_assoc()) {
    $historial[] = $fila;
}

$sql->close();
mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glicemias - HomeCare Dialysis</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="login.html">Inicio de Sesion</a></li>
            <li><a href="general.html">Panel General</a></li>
            <li><a href="balance.php">Balance Hídrico</a></li>
            <li><a href="glicemias.php" class="activo">Análisis Glicemia</a></li>
            <li><a href="reportes.html">Analítica Visual</a></li>
            <li><a href="../../backend/api/logout.php" class="btn-cerrar-sesion">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="contenido-principal">
        <h2>Módulo de Glicemias</h2>

        <form method="post" action="../../backend/api/glicemia.php">
        <label>Valor de glucosa (mg/dL)</label>
        <input type="number" id="glucosa" name="glucosa" required><br>

        <label>Momento de medición</label>
        <select id="momento" name="momento" required>
            <option value="ayunas">Ayunas</option>
            <option value="antes">Antes de la comida</option>
            <option value="despues">2 horas después de la comida</option>
        </select><br>

        <button id="analizar" type="submit">Analizar y Guardar</button>
        </form>

        <h3>Historial</h3>
        <div class="container">
            <?php foreach ($historial as $fila) { ?>
            <div class="card">
                <p class="card-valor"><?php echo htmlspecialchars($fila['valor_glucosa']); ?> mg/dL</p>
                <p class="card-detalle"><?php echo htmlspecialchars(ucfirst($fila['momento'])); ?> · <?php echo htmlspecialchars($fila['created_at']); ?></p>
                <span class="card-estado"><?php echo htmlspecialchars($fila['diagnostico']); ?></span>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>

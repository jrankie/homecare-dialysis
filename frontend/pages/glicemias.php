<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.html");
    exit();
}

require_once "../../backend/config/dbconn.php";
$paciente_id = $_SESSION['paciente_id'];

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
            <li><a href="general.php">Panel General</a></li>
            <li><a href="balance.php">Balance Hídrico</a></li>
            <li><a href="glicemias.php" class="activo">Análisis Glicemia</a></li>
            <li><a href="reportes.php">Analítica Visual</a></li>
            <li><a href="../../backend/php/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="contenido glicemia">
        <div class="modulo-glicemia">
        <h2>Módulo de Glicemias</h2>

        <form method="post" action="../../backend/php/glicemia.php">
        <label>Valor de glucosa (mg/dL)</label>
        <input type="number" id="glucosa" name="glucosa" required><br>

        <label>Momento de medición</label>
        <select id="momento" name="momento" required>
            <option value="ayunas">Ayunas</option>
            <option value="antes">Antes de la comida</option>
            <option value="despues">2 horas después de la comida</option>
        </div>
</div>
        </select><br>

        <div class="boton-glicemia">
        <button id="analizar" type="submit" class="glicemia-btn">Analizar y Guardar</button>
        </div>
        </form>

        <h3>Historial</h3>
        <div class="container">
            <?php
            foreach ($historial as $fila) {
                $valor = htmlspecialchars($fila['valor_glucosa']);
                $momento = htmlspecialchars(ucfirst($fila['momento']));
                $fecha = htmlspecialchars($fila['created_at']);
                $diagnostico = htmlspecialchars($fila['diagnostico']);
                
                echo "<div class='card'>
                    <p class='card-valor'>$valor mg/dL</p>
                    <p class='card-detalle'>$momento · $fecha</p>
                    <span class='card-estado'>$diagnostico</span>
                </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>

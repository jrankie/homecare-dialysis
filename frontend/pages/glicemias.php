<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.html");
    exit();
}

require "../../backend/config/dbconn.php";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="login.html">Inicio de Sesion</a></li>
            <li><a href="general.php">Panel General</a></li>
            <li><a href="balance.php">Balance Hídrico</a></li>
            <li><a href="glicemias.php" class="activo">Análisis Glicemia</a></li>
            <li><a href="chart.html">Analítica Visual</a></li>
            <li><a href="../../backend/php/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="contenido glicemia">
<div class="modulo-glicemia">

    <h2>Módulo de Glicemias</h2>

    <form method="post" action="../../backend/php/glicemia.php">

        <label>Valor de glucosa (mg/dL)</label>

        <input type="number" id="glucosa" name="glucosa" required>

        <label>Momento de medición</label>

        <select id="momento" name="momento" required>

            <option value="ayunas">Ayunas</option>
            <option value="antes">Antes de la comida</option>
            <option value="despues">2 horas después de la comida</option>

        </select>

        <div class="boton-glicemia">

            <button id="analizar" type="submit" class="glicemia-btn">

                Analizar y Guardar

            </button>

        </div>

    </form>

</div>

        <div class="modulo-glicemia" style="margin-top: 30px; width: 100%; max-width: 600px; padding: 30px;">
            <h3>Gráfico de Tendencia</h3>
            <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label style="color: #006064; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 8px;">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" required style="padding: 10px; border: 1px solid #ccc; border-radius: 8px; width: 100%;">
                </div>
                <div style="flex: 1;">
                    <label style="color: #006064; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 8px;">Fecha Fin</label>
                    <input type="date" id="fecha_fin" required style="padding: 10px; border: 1px solid #ccc; border-radius: 8px; width: 100%;">
                </div>
                <div>
                    <button type="button" id="btnGraficarGlicemia" class="glicemia-btn">Graficar</button>
                </div>
            </div>
            <div style="width: 100%; height: 350px; background: white; padding: 15px; border-radius: 12px;">
                <canvas id="glicemiaChart"></canvas>
            </div>
        </div>

        <script src="../js/glicemias.js"></script>

        <h3>Historial</h3>
        <div class="cards-glicemia">
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

<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'paciente') {
    header("Location: login.html");
    exit();
}

require_once "../../backend/config/dbconn.php";
$paciente_id = $_SESSION['paciente_id'];
$nombre_paciente = $_SESSION['nombre'];


$sql = $conectar->prepare("SELECT fecha_tratamiento, SUM(infusion) as total_infusion, SUM(drenaje) as total_drenaje, SUM(balance) as total_balance 
                           FROM recambios 
                           WHERE paciente_id = ? 
                           GROUP BY fecha_tratamiento 
                           ORDER BY fecha_tratamiento DESC");
$sql->bind_param("i", $paciente_id);
$sql->execute();
$resultado = $sql->get_result();

$historial = array();
while ($fila = $resultado->fetch_assoc()) {
    $total_balance = $fila['total_balance'];
    if ($total_balance <= 0) {
        $estado = "Favorable";
    } elseif ($total_balance <= 2000) {
        $estado = "Retención moderada";
    } else {
        $estado = "Excesiva retención";
    }
    
    $fila['estado'] = $estado;
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
    <title>Balance Hídrico - HomeCare Dialysis</title>
    <link rel="stylesheet" href="../css/style.css">

</head>

<body>
    <nav class="navbar">
        <ul>
            <li><a href="login.html">Inicio de Sesion</a></li>
            <li><a href="general.php">Panel General</a></li>
            <li><a href="balance.php" class="activo" >Balance Hídrico</a></li>
            <li><a href="glicemias.php">Análisis Glicemia</a></li>
            <li><a href="reportes.php">Analítica Visual</a></li>
            <li><a href="../../backend/php/logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="contenido balance">
        <h2>REPORTE DE BALANCE HÍDRICO - DIÁLISIS PERITONEAL</h2>

        <form method="post" action="../../backend/php/recambios.php">

        <div class="titulo">
            <div class="fila">
                <div class="columna">
                    <label>Paciente</label>
                    <input type="text" name="username" value="<?php echo $nombre_paciente; ?>" readonly>
                </div>
                <div class="columna">
                    <label>Fecha de Tratamiento</label>
                    <input type="date" name="fecha_tratamiento" required>
                </div>
                <div class="columna">
                    <label>Sistema</label>
                    <select name="sistema">
                        <option value="Baxter">Baxter</option>
                        <option value="Fresenius Medical Care">Fresenius Medical Care</option>
                    </select>
                </div>
                <div class="columna">
                    <label>P/A</label>
                    <input type="text" name="presion_arterial">
                </div>
            </div>
            <div class="fila">
                <div class="columna">
                    <label>Pulso</label>
                    <input type="number" name="pulso">
                </div>
                <div class="columna">
                    <label>Fecha de Registro</label>
                    <input type="date" name="fecha">
                </div>
                <div class="columna">
                    <label>Hora de Registro</label>
                    <input type="time" name="hora">
                </div>
            </div>
        </div>

        <div>
            <table class="tbalance">
                <thead>
                <tr>
                    <th>Recambio</th>
                    <th>Concentración</th>
                    <th>Infusión</th>
                    <th>Drenaje</th>
                    <th>Cualidad</th>
                    <th>Balance</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>
                        <select id="concentracion1" name="concentracion1" >
                            <option value="1.5%">1.5%</option>
                            <option value="2.5%">2.5%</option>
                            <option value="7.5%">7.5%</option>
                        </select>
                    </td>
                    <td>
                        <label>ml</label>
                        <select>
                            <option value="2000">2000</option>
                        </select>
                    </td>
                    <td>
                        <label for="drenar1">ml.</label>
                        <input type="number" id="drenar1" name="drenar1" required>
                    </td>
                    <td>
                        <select id="cualidad1" name="cualidad1">
                            <option value="Claro">Claro</option>
                            <option value="Turbio">Turbio</option>
                        </select>
                    </td>
                    <td id="balance1"></td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>
                        <select id="concentracion2" name="concentracion2">
                            <option value="1.5%">1.5%</option>
                            <option value="2.5%">2.5%</option>
                            <option value="7.5%">7.5%</option>
                        </select>
                    </td>
                    <td>
                        <label>ml</label>
                        <select>
                            <option value="2000">2000</option>
                        </select>
                    </td>
                    <td>
                        <label for="drenar2">ml.</label>
                        <input type="number" id="drenar2" name="drenar2" required>
                    </td>
                    <td>
                        <select id="cualidad2" name="cualidad2">
                            <option value="Claro">Claro</option>
                            <option value="Turbio">Turbio</option>
                        </select>
                    </td>
                    <td id="balance2"></td>
                </tr>

                <tr>
                    <td>3</td>
                    <td>
                        <select id="concentracion3" name="concentracion3">
                            <option value="1.5%">1.5%</option>
                            <option value="2.5%">2.5%</option>
                            <option value="7.5%">7.5%</option>
                        </select>
                    </td>
                    <td>
                        <label>ml</label>
                        <select>
                            <option value="2000">2000</option>
                        </select>
                    </td>
                    <td>
                        <label for="drenar3">ml.</label>
                        <input type="number" id="drenar3" name="drenar3" required>
                    </td>
                    <td>
                        <select id="cualidad3" name="cualidad3">
                            <option value="Claro">Claro</option>
                            <option value="Turbio">Turbio</option>
                        </select>
                    </td>
                    <td id="balance3"></td>
                </tr>

                <tr>
                    <td>4</td>
                    <td>
                        <select id="concentracion4" name="concentracion4">
                            <option value="1.5%">1.5%</option>
                            <option value="2.5%">2.5%</option>
                            <option value="7.5%">7.5%</option>
                        </select>
                    </td>
                    <td>
                        <label>ml</label>
                        <select>
                            <option value="2000">2000</option>
                        </select>
                    </td>
                    <td>
                        <label for="drenar4">ml.</label>
                        <input type="number" id="drenar4" name="drenar4" required>
                    </td>
                    <td>
                        <select id="cualidad4" name="cualidad4">
                            <option value="Claro">Claro</option>
                            <option value="Turbio">Turbio</option>
                        </select>
                    </td>
                    <td id="balance4"></td>
                </tr>

                <tr>
                    <td>Total</td>
                    <td></td>
                    <td id="totalInfusion"></td>
                    <td id="totalDrenaje"></td>
                    <td></td>
                    <td id="totalBalance"></td>
                </tr>
                </thead>
            </table>
            <div class ="btn">
            <button id="calcular" type="button" class="btn-calcular">Calcular</button>
            <button type="submit"class="btn-guardar">Guardar en Base de Datos</button>
            </div>
            <h4 id="analisis">Análisis de resultados para el paciente:</h4>
        </form>
        <script src="../js/main.js"></script>

        <h3>Historial</h3>
        <div>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Balance Diario</th>
                    <th>Estado</th>
                </tr>
                <?php
                foreach ($historial as $fila) {
                    $fecha = $fila['fecha_tratamiento'];
                    $paciente = $nombre_paciente;
                    $balance = $fila['total_balance'];
                    $estado = $fila['estado'];
                    
                    echo "<tr>
                        <td>$fecha</td>
                        <td>$paciente</td>
                        <td>$balance ml</td>
                        <td>$estado</td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

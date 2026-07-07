<?php
/**
 * Helper para estandarizar respuestas JSON en el sistema
 */
function responderJSON($datos, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json');
    echo json_encode($datos);
    exit();
}
?>

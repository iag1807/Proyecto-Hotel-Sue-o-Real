<?php

$entrada = $_POST['fecha_ingreso'] ?? '';
$salida = $_POST['fecha_salida'] ?? '';
$personas = $_POST['numero_personas'] ?? '';

if (empty($entrada) || empty($salida) || empty($personas)) {
    header("Location: cliente.php");
    exit();
}

header("Location: cliente.php?entrada=$entrada&salida=$salida&personas=$personas");
exit();

?>
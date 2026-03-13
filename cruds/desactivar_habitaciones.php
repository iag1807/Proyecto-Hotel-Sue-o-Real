<?php
require_once '../conexion.php';
$id_habitacion = $_GET['id_habitacion'];

$sql_select = "SELECT estado FROM habitaciones WHERE id_habitacion=?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id_habitacion);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$fila = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if ($fila) {
    $nuevo_estado = ($fila['estado'] == 'activa') ? 'mantenimiento' : 'activa';
    $sql_update = "UPDATE habitaciones SET estado=? WHERE id_habitacion=?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $nuevo_estado, $id_habitacion);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}

header("Location: ver_habitaciones.php");
exit();
?>
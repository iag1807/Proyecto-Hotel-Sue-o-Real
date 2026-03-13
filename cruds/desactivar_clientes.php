<?php 
require_once '../conexion.php';
$documento = $_GET['documento'];

$sql_select = "SELECT estado FROM clientes WHERE documento=?";
$stmt_select = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $documento);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$fila = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if ($fila) {
    $nuevo_estado = ($fila['estado'] == 'activo') ? 'inactivo' : 'activo';
    $sql_update = "UPDATE clientes SET estado=? WHERE documento=?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $nuevo_estado, $documento);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}

header("Location: ver_clientes.php");
exit();
?>
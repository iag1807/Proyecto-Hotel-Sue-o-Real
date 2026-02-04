<?php
require_once '../conexion.php';
$numero = $_GET['numero'];
$sql = "UPDATE habitaciones SET estado='inactivo' WHERE numero=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $numero);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("Location: ver_habitaciones.php");
exit();
?>
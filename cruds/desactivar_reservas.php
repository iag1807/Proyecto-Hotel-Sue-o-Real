<?php
require_once '../conexion.php';
$id_reserva = $_GET['id_reserva'];
$sql = "UPDATE reservas SET estado='inactivo' WHERE id_reserva=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_reserva);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("Location: ver_reservas.php");
exit();
?>
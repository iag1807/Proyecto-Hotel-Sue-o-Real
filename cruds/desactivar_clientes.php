<?php 
require_once '../conexion.php';
$documento = $_GET['documento'];
$sql = "UPDATE clientes SET estado='inactivo' WHERE documento=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $documento);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("Location: ver_clientes.php");
exit();
?>
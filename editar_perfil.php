<?php
require_once 'conexion.php';
session_start();

if (isset($_POST['actualizar_datos'])) {

  $nombre = $_POST['nombre'];
  $apellidos = $_POST['apellidos'];
  $correo = $_POST['correo'];
  $celular = $_POST['celular'];
  $documento = $_SESSION['documento'];

  $sql_update = "UPDATE clientes 
                 SET nombre=?, apellidos=?, correo=?, celular=? 
                 WHERE documento=?";

  $stmt = mysqli_prepare($conn, $sql_update);
  mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $apellidos, $correo, $celular, $documento);

  if (mysqli_stmt_execute($stmt)) {
      echo "<script>alert('Datos actualizados correctamente'); window.location='perfil.php';</script>";
  } else {
      echo "<script>alert('Error al actualizar'); window.location='perfil.php';</script>";
  }

  mysqli_stmt_close($stmt);
}
?>
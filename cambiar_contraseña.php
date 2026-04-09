<?php
if (isset($_POST['cambiar_password'])) {

require_once 'conexion.php';
session_start();

  $actual = $_POST['actual'];
  $nueva = $_POST['nueva'];
  $confirmar = $_POST['confirmar'];
  $documento = $_SESSION['documento'];

  $sql = "SELECT clave FROM clientes WHERE documento=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $documento);
  mysqli_stmt_execute($stmt);
  $resultado = mysqli_stmt_get_result($stmt);
  $fila = mysqli_fetch_assoc($resultado);

  // Verificar contraseña actual
  if (!password_verify($actual, $fila['clave'])) {
      echo "<script>alert('Contraseña actual incorrecta'); window.location='perfil.php';</script>";
  } 
  elseif ($nueva !== $confirmar) {
      echo "<script>alert('Las contraseñas no coinciden'); window.location='perfil.php';</script>";
  } 
  else {

      $nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);

      $sql_update = "UPDATE clientes SET clave=? WHERE documento=?";
      $stmt = mysqli_prepare($conn, $sql_update);
      mysqli_stmt_bind_param($stmt, "si", $nuevaHash, $documento);

      if (mysqli_stmt_execute($stmt)) {
          echo "<script>alert('Contraseña actualizada correctamente'); window.location='perfil.php';</script>";
      } else {
          echo "<script>alert('Error al actualizar'); window.location='perfil.php';</script>";
      }
  }
}
?>
<?php
session_start();

if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'recepcionista'){
    header("location: index.php");
    exit();
}

require_once 'conexion.php';
$sql= "SELECT nombre, apellidos FROM clientes WHERE documento=?";
$stmt= mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['documento']);
mysqli_stmt_execute($stmt);
$resultado=mysqli_stmt_get_result($stmt);
$datos= mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel recepcionista</title>
    <link rel="shortcut icon" href="imagenes/LogoHotel.png">
</head>
<body>
    <h1>Bienvenid@ <?php echo htmlspecialchars($datos['nombre']) ," ", htmlspecialchars($datos['apellidos']); ?></h1>
    <a href="iniciodesesion/cerrarsesion.php"><button>Cerrar sesion</button></a>
</body>
</html>
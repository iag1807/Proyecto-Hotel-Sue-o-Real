<?php
session_start();

if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin'){
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
    <title>Panel administradores</title>
    <link rel="shortcut icon" href="imagenes/LogoHotel.png">
    <link rel="stylesheet" href="estilos/style-admin.css">
</head>
<body>
    <?php if(isset($_SESSION['success'])) : ?>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <header>
        <img class="logo" src="imagenes/LogoHotel.png" alt="">
        <h1>Bienvenid@ <br> <?php echo htmlspecialchars($datos['nombre']) ," ", htmlspecialchars($datos['apellidos']); ?></h1>
        <a href="iniciodesesion/cerrarsesion.php"><button class="btn">Cerrar sesion</button></a>
    </header>
    
    <a href="cruds/ver_clientes.php"><button>Ver clientes</button></a><br><br>
    <a href="cruds/ver_habitaciones.php"><button>Ver habitaciones</button></a><br><br>
    <a href="cruds/ver_reservas.php"><button>Ver reservas</button></a><br><br>
    
</body>
</html>
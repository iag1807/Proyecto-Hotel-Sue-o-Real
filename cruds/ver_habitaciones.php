<?php
require_once '../conexion.php';

$sql= "SELECT * FROM habitaciones ORDER BY CASE tipo_habitacion WHEN 'sencilla' THEN 1 WHEN 'bañera' THEN 2 WHEN 'jacuzzi' THEN 3 WHEN 'doble' THEN 4 WHEN 'triple' THEN 5 WHEN 'multiple' THEN 6 END, numero";
$resultado= mysqli_query($conn, $sql);
$total= mysqli_num_rows($resultado);

$current_page = $_SERVER['PHP_SELF'];
$active_inicio = ($current_page == '/HotelSueñoReal/admin.php') ? ' class="active"' : '';
$active_habitaciones = ($current_page == '/HotelSueñoReal/cruds/ver_habitaciones.php') ? ' class="active"' : '';
$active_reservas = ($current_page == '/HotelSueñoReal/cruds/ver_reservas.php') ? ' class="active"' : '';
$active_clientes = ($current_page == '/HotelSueñoReal/cruds/ver_clientes.php') ? ' class="active"' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de Administrador</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../estilos/style-cruds.css">
<link rel="shortcut icon" href="../imagenes/LogoHotel.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="../imagenes/LogoHotel.png" alt="" class="logo-img">
  </div>

  <ul class="nav-links">
    <li><a href="../admin.php"<?php echo $active_inicio; ?>>
      <span class="icon">⌂</span> Inicio
    </a></li>
    <li><a href="ver_habitaciones.php"<?php echo $active_habitaciones; ?>>
      <span class="icon">◫</span> Habitaciones
    </a></li>
    <li><a href="ver_reservas.php"<?php echo $active_reservas; ?>>
      <span class="icon">◻</span> Reservaciones
    </a></li>
    <li><a href="ver_clientes.php"<?php echo $active_clientes; ?>>
      <span class="icon">◈</span> Huéspedes
    </a></li>
  </ul>

  <div class="sidebar-bottom">
    <a href="iniciodesesion/cerrarsesion.php" class="logout-btn">
      <span>⏻</span> Cerrar sesión
    </a>
  </div>
</aside>


<main class="main">

  
  <nav class="topnav">
    <span class="topnav-title">Panel de Administrador</span>
    <div class="topnav-right">
      <span class="topnav-date">
        
      <p id="fecha"></p>
        <script>
            let fecha = new Date();
            let opciones = { year: 'numeric', month: 'long', day: 'numeric' };

            document.getElementById("fecha").innerHTML =
            fecha.toLocaleDateString('es-ES', opciones);
        </script>
        </span>
    </div>
  </nav>

  
    <div class="bottom-grid">

      
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Habitaciones</span>
          <a href="registrar_habitacion.php" class="panel-action">Agregar Habitacion</a>
        </div>
        <table class="res-table">
          <thead>
            <tr>
              <th>Numero</th>
              <th>Tipo de habitacion</th>
              <th>Capacidad</th>
              <th>Precio</th>
              <th>Descripcion</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['numero']); ?></td>
                <td><?php echo htmlspecialchars($fila['tipo_habitacion']); ?></td>
                <td><?php echo htmlspecialchars($fila['capacidad']); ?></td>
                <td><?php echo htmlspecialchars($fila['precio']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a class="badge badge-confirmada" href="editar_habitaciones.php?id_habitacion=<?php echo $fila['id_habitacion']; ?>">Editar</a>
                    <a class="badge <?php echo ($fila['estado'] == 'activa') ? 'badge-cancelada' : 'badge-confirmada'; ?>" href="desactivar_habitaciones.php?id_habitacion=<?php echo $fila['id_habitacion']; ?>"><?php echo ($fila['estado'] == 'activa') ? 'Mantenimiento' : 'Disponible'; ?></a>
                </td>
            <?php endwhile; ?>
            </tr>
          </tbody>
        </table>
    </div>
</main>
</body>
</html>
            
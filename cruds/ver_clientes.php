<?php
require_once '../conexion.php';

$sql= "SELECT * FROM clientes ORDER BY apellidos ASC";
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
          <span class="panel-title">Huespedes</span>
          <a href="registrar_cliente.php" class="panel-action">Agregar Huesped</a>
        </div>
        <table class="res-table">
          <thead>
            <tr>
              <th>Documento</th>
              <th>Tipo de documento</th>
              <th>Nombre</th>
              <th>Apellidos</th>
              <th>Correo</th>
              <th>Genero</th>
              <th>Celular</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['documento']); ?></td>
                <td><?php echo htmlspecialchars($fila['tipo_documento']); ?></td>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                <td><?php echo htmlspecialchars($fila['genero']); ?></td>
                <td><?php echo htmlspecialchars($fila['celular']); ?></td>
                <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a class="badge badge-confirmada" href="editar_clientes.php?documento=<?php echo $fila['documento']; ?>">Editar</a>
                    <a class="badge <?php echo ($fila['estado'] == 'activo') ? 'badge-cancelada' : 'badge-confirmada'; ?>" href="desactivar_clientes.php?documento=<?php echo $fila['documento']; ?>"><?php echo ($fila['estado'] == 'activo') ? 'Desactivar' : 'Activar'; ?></a>
                </td>
            <?php endwhile; ?>
            </tr>
          </tbody>
        </table>
    </div>
</main>
</body>
</html>
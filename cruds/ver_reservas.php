<?php
require_once '../conexion.php';

$sql = "SELECT r.*, c.documento, c.nombre, c.apellidos, h.id_habitacion, h.numero 
        FROM reservas AS r
        JOIN clientes AS c ON r.cliente_documento = c.documento
        JOIN habitaciones AS h ON r.habitacion_id = h.id_habitacion
        ORDER BY r.fecha_creacion DESC";

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
          <span class="panel-title">Reservaciones</span>
          <a href="registrar_reserva.php" class="panel-action">Agregar reserva</a>
        </div>
        <table class="res-table">
          <thead>
            <tr>
              <th>Documento cliente</th>
              <th>Nombre cliente</th>
              <th>Fecha de ingreso</th>
              <th>Fecha de salida</th>
              <th>Numero de personas</th>
              <th>Habitacion</th>
              <th>Estado</th>
              <th>Comprobante de pago</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['documento']); ?></td>
                <td><?php echo htmlspecialchars("{$fila['nombre']} {$fila['apellidos']}"); ?></td>
                <td><?php echo htmlspecialchars($fila['fecha_ingreso']); ?></td>
                <td><?php echo htmlspecialchars($fila['fecha_salida']); ?></td>
                <td><?php echo htmlspecialchars($fila['numero_personas']); ?></td>
                <td><?php echo htmlspecialchars($fila['numero']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <?php if ($fila['comprobante_pago']): ?>
                        <a href="../uploads/comprobantes/<?php echo htmlspecialchars($fila['comprobante_pago']); ?>" target="_blank" class="badge badge-confirmada">Ver comprobante</a>
                    <?php else: ?>
                        <span class="badge badge-cancelada">Sin comprobante</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="badge badge-confirmada" href="editar_reservas.php?id_reserva=<?php echo $fila['id_reserva']; ?>">Editar</a>
                    <a class="badge badge-cancelada" href="desactivar_reservas.php?id_reserva=<?php echo $fila['id_reserva']; ?>">Cancelar</a>
                    <a class="badge badge-finalizada" href="finalizar_reservas.php?id_reserva=<?php echo $fila['id_reserva']; ?>">Finalizar</a>
                </td>
            <?php endwhile; ?>
            </tr>
          </tbody>
        </table>
    </div>
</main>
</body>
</html>
<?php
session_start();
$documento = $_SESSION['documento'];

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
  header("location: index.html");
  exit();
}

require_once 'conexion.php';

$sql = "SELECT nombre, apellidos, genero FROM clientes WHERE documento=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['documento']);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$datos = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);


$saludo = ($datos['genero'] === 'femenino') ? 'Bienvenida' : 'Bienvenido';

$current_page        = $_SERVER['PHP_SELF'];
$active_inicio       = ($current_page == '/HotelSueñoReal/cliente.php')                  ? ' class="active"' : '';
$active_reservar     = ($current_page == '/HotelSueñoReal/reserva.php')                  ? ' class="active"' : '';
$active_reservas     = ($current_page == '/HotelSueñoReal/mis_reservas.php')             ? ' class="active"' : '';
$active_perfil       = ($current_page == '/HotelSueñoReal/perfil.php')                   ? ' class="active"' : '';


// Proxima reserva
$sql_proxima = "SELECT reservas.*, habitaciones.tipo_habitacion, habitaciones.numero
FROM reservas
INNER JOIN habitaciones 
ON reservas.habitacion_id = habitaciones.id_habitacion
WHERE reservas.cliente_documento = '$documento'
AND reservas.fecha_ingreso >= CURDATE()
ORDER BY reservas.fecha_ingreso ASC
LIMIT 1";

$resultado_proxima = mysqli_query($conn, $sql_proxima);



//Historial de reservas

$sql = "SELECT r.*, h.numero, h.tipo_habitacion 
        FROM reservas r
        INNER JOIN habitaciones h ON r.habitacion_id = h.id_habitacion
        WHERE r.cliente_documento = '$documento'
        ORDER BY r.fecha_creacion DESC";

$resultado = mysqli_query($conn, $sql);

function getBadgeEstado($estado) {
    $map = [
        'pendiente' => 'badge-pendiente',
        'confirmada' => 'badge-confirmada',
        'finalizada'  => 'badge-finalizada',
        'cancelada'  => 'badge-cancelada',
    ];
    return $map[$estado] ?? 'badge-pendiente';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Sueño Real</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilos/style-clientes.css">
  <link rel="shortcut icon" href="imagenes/LogoHotel.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&display=swap" rel="stylesheet"/>
</head>

<body>

  <aside class="sidebar">
    <div class="sidebar-logo">
      <img src="./imagenes/LogoHotel.png" alt="" class="logo-img">
    </div>

    <ul class="nav-links">
      <li><a href="cliente.php" <?php echo $active_inicio; ?>>
          <span class="icon">⌂</span> Inicio
        </a></li>
      <li><a href="mis_reservas.php" <?php echo $active_reservas; ?>>
          <span class="icon">◻</span>Mis reservas
        </a></li>
      <li><a href="perfil.php" <?php echo $active_perfil; ?>>
          <span class="icon">Ω</span>Mi perfil
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
      <div class="topnav-right">
        <span class="topnav-date">
          <p id="fecha"></p>
          <script>
            let fecha = new Date();
            let opciones = {
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            };
            document.getElementById("fecha").innerHTML = fecha.toLocaleDateString('es-ES', opciones);
          </script>
        </span>
      </div>
    </nav>

    <section class="hero">
      <div class="hero-bg"></div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-text">
          <h2><?php echo $saludo; ?><br>
            <em><?php echo htmlspecialchars($datos['nombre']), " ", htmlspecialchars($datos['apellidos']); ?></em>
          </h2>
        </div>
      </div>
    </section>

    <div class="dashboard-reservas">

      <h2>Tu próxima reserva</h2>

      <?php
      $reserva = mysqli_fetch_assoc($resultado_proxima);
      ?>

      <div class="proxima-reserva">

        <?php if ($reserva) { ?>

          <div class="info-reserva">
            <h3>Habitacion <?php echo $reserva['tipo_habitacion']; ?></h3>

            <p>Fecha de entrada: <?php echo ($reserva['fecha_ingreso']); ?></p>

            <p>Fecha de salida: <?php echo ($reserva['fecha_salida']); ?></p>

            <p>Huéspedes: <?php echo $reserva['numero_personas']; ?></p>

            <p>Habitacion: <?php echo $reserva['numero'], " • ", $reserva['tipo_habitacion']; ?></p>

            <span class="badge <?php echo getBadgeEstado($reserva['estado']); ?>">
              <?php echo $reserva['estado'] ?>
            </span>
          </div>

        <?php } else { ?>

          <div class="info-reserva">
            <h3>No tienes reservas próximas</h3>
            <p>Cuando realices una reserva aparecerá aquí.</p>
          </div>

        <?php } ?>

      </div>


      <div class="bottom-grid">

      
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Historial de reservas</span>
        </div>
        <table class="res-table">
          <thead>
            <tr>
              <th>Fecha de ingreso</th>
              <th>Fecha de salida</th>
              <th>Numero de personas</th>
              <th>Habitacion</th>
              <th>Estado</th>
              <th>Comprobante de pago</th>
            </tr>
          </thead>
          <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['fecha_ingreso']); ?></td>
                <td><?php echo htmlspecialchars($fila['fecha_salida']); ?></td>
                <td><?php echo htmlspecialchars($fila['numero_personas']); ?></td>
                <td><?php echo htmlspecialchars($fila['numero']), " - ", htmlspecialchars($fila['tipo_habitacion']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <?php if ($fila['comprobante_pago']): ?>
                        <a href="./<?php echo htmlspecialchars($fila['comprobante_pago']); ?>" target="_blank" class="badge badge-confirmada">Ver comprobante</a>
                    <?php else: ?>
                        <span class="badge badge-cancelada">Sin comprobante</span>
                    <?php endif; ?>
                </td>
            <?php endwhile; ?>
            </tr>
          </tbody>
        </table>
    </div>
  </main>
</body>

</html>
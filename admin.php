<?php
session_start();

if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin'){
    header("location: index.html");
    exit();
}

require_once 'conexion.php';

$sql= "SELECT nombre, apellidos, genero FROM clientes WHERE documento=?";
$stmt= mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['documento']);
mysqli_stmt_execute($stmt);
$resultado= mysqli_stmt_get_result($stmt);
$datos= mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

$saludo = ($datos['genero'] === 'femenino') ? 'Bienvenida' : 'Bienvenido';

$hoy = date('Y-m-d');
$sql_reservas = "
    SELECT r.id_reserva, r.fecha_ingreso, r.fecha_salida, r.estado, r.fecha_creacion,
           c.nombre, c.apellidos,
           h.numero, h.tipo_habitacion
    FROM reservas r
    JOIN clientes    c ON r.cliente_documento = c.documento
    JOIN habitaciones h ON r.habitacion_id    = h.id_habitacion
    WHERE r.fecha_ingreso = ?
    ORDER BY r.fecha_creacion DESC
";
$stmt_res = mysqli_prepare($conn, $sql_reservas);
mysqli_stmt_bind_param($stmt_res, 's', $hoy);
mysqli_stmt_execute($stmt_res);
$res_reservas = mysqli_stmt_get_result($stmt_res);
mysqli_stmt_close($stmt_res);

$sql_hab  = "SELECT id_habitacion, numero, tipo_habitacion, estado FROM habitaciones ORDER BY CASE tipo_habitacion WHEN 'sencilla' THEN 1 WHEN 'bañera' THEN 2 WHEN 'jacuzzi' THEN 3 WHEN 'doble' THEN 4 WHEN 'triple' THEN 5 WHEN 'multiple' THEN 6 END, numero";
$res_habs = mysqli_query($conn, $sql_hab);
$habitaciones = mysqli_fetch_all($res_habs, MYSQLI_ASSOC);


$sql_activas = "
    SELECT DISTINCT habitacion_id
    FROM reservas
    WHERE estado IN ('confirmada')
      AND fecha_ingreso <= ?
      AND fecha_salida   > ?
";
$stmt2 = mysqli_prepare($conn, $sql_activas);
mysqli_stmt_bind_param($stmt2, 'ss', $hoy, $hoy);
mysqli_stmt_execute($stmt2);
$res_activas  = mysqli_stmt_get_result($stmt2);
$habs_activas = [];
while ($row = mysqli_fetch_assoc($res_activas)) {
    $habs_activas[$row['habitacion_id']] = true;
}
mysqli_stmt_close($stmt2);

$habs_por_tipo = [];
foreach ($habitaciones as $hab) {
    $habs_por_tipo[$hab['tipo_habitacion']][] = $hab;
}

$orden_tipos = ['sencilla', 'bañera', 'jacuzzi', 'doble', 'triple', 'multiple'];

function getClaseHab($hab, $habs_activas) {
    if ($hab['estado'] === 'mantenimiento') return 'mantenimiento';
    if (isset($habs_activas[$hab['id_habitacion']])) return 'ocupada';
    return 'disponible';
}

function getBadgeEstado($estado) {
    $map = [
        'confirmada' => 'badge-confirmada',
        'finalizada'  => 'badge-finalizada',
        'cancelada'  => 'badge-cancelada',
    ];
    return $map[$estado] ?? 'badge-confirmada';
}

$current_page        = $_SERVER['PHP_SELF'];
$active_inicio       = ($current_page == '/HotelSueñoReal/admin.php')                    ? ' class="active"' : '';
$active_habitaciones = ($current_page == '/HotelSueñoReal/cruds/ver_habitaciones.php')   ? ' class="active"' : '';
$active_reservas     = ($current_page == '/HotelSueñoReal/cruds/ver_reservas.php')       ? ' class="active"' : '';
$active_clientes     = ($current_page == '/HotelSueñoReal/cruds/ver_clientes.php')       ? ' class="active"' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel de Administrador</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="estilos/style-admin.css">
<link rel="shortcut icon" href="imagenes/LogoHotel.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="./imagenes/LogoHotel.png" alt="" class="logo-img">
  </div>

  <ul class="nav-links">
    <li><a href="admin.php"<?php echo $active_inicio; ?>>
      <span class="icon">⌂</span> Inicio
    </a></li>
    <li><a href="./cruds/ver_habitaciones.php"<?php echo $active_habitaciones; ?>>
      <span class="icon">◫</span> Habitaciones
    </a></li>
    <li><a href="./cruds/ver_reservas.php"<?php echo $active_reservas; ?>>
      <span class="icon">◻</span> Reservaciones
    </a></li>
    <li><a href="./cruds/ver_clientes.php"<?php echo $active_clientes; ?>>
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
    <span class="topnav-title">Administrador</span>
    <div class="topnav-right">
      <span class="topnav-date">
        <p id="fecha"></p>
        <script>
          let fecha = new Date();
          let opciones = { year: 'numeric', month: 'long', day: 'numeric' };
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
        <p>Vista general del día</p>
      </div>
    </div>
  </section>

  <div class="content">
    <div class="bottom-grid">

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Mapa de habitaciones</span>
          <a href="./cruds/ver_habitaciones.php" class="panel-action">Gestionar</a>
        </div>

        <div class="rooms-legend">
          <div class="legend-item"><span class="legend-dot ld-ocupada"></span>Reservada</div>
          <div class="legend-item"><span class="legend-dot ld-disponible"></span>Disponible</div>
          <div class="legend-item"><span class="legend-dot ld-mantenimiento"></span>Mantenimiento</div>
        </div>

        <div style="padding: 1.25rem 1.5rem 0.5rem;">
            <?php foreach ($orden_tipos as $tipo_actual):
              $grupo = $habs_por_tipo[$tipo_actual];
            ?>
              <div class="room-group">
                <div class="room-group-title"><?php echo $tipo_actual; ?></div>
                <div class="rooms-grid" style="padding:0; margin-bottom:0.25rem">
                  <?php foreach ($grupo as $hab):
                    $clase = getClaseHab($hab, $habs_activas);
                  ?>
                    <div class="room-cell <?php echo $clase; ?>">
                      <div class="room-num"><?php echo htmlspecialchars($hab['numero']); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">Reservas para hoy </span>
        </div>
        <table class="res-table">
          <thead>
            <tr>
              <th>Huésped</th>
              <th>Habitación</th>
              <th>Ingreso</th>
              <th>Salida</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($res_reservas) === 0): ?>
              <tr>
                <td colspan="5" style="text-align:center; padding:2rem; opacity:0.4;">
                  Sin reservas programadas para hoy
                </td>
              </tr>
            <?php else: ?>
              <?php while ($r = mysqli_fetch_assoc($res_reservas)): ?>
                <?php
                  
                  $ingreso = date('d M', strtotime($r['fecha_ingreso']));
                  $salida = date('d M', strtotime($r['fecha_salida']));
                  $badge  = getBadgeEstado($r['estado']);
                  $label  = $r['estado'];
                ?>
                <tr>
                  <td><?php echo htmlspecialchars("{$r['nombre']} {$r['apellidos']}"); ?></td>
                  <td><?php echo htmlspecialchars(" {$r['numero']} · " . ucfirst($r['tipo_habitacion'])); ?></td>
                  <td><?php echo $ingreso; ?></td>                  
                  <td><?php echo $salida; ?></td>
                  <td><span class="badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</main>
</body>
</html>
<?php
session_start();
$documento = $_SESSION['documento'];

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
  header("location: index.html");
  exit();
}

require_once 'conexion.php';

$sql = "SELECT * FROM clientes WHERE documento=?";
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

    <h2 class="perfil">Mi Perfil</h2>

    <section class="perfil-grid">
      <div class="perfil-1">
        <div class="perfil-2">
          <div class="perfil-icon"><img src="./imagenes/icono-usuario.png" alt=""></div>
          <div>
            <label class="perfil-name"><?php echo htmlspecialchars($datos['nombre']), " ", htmlspecialchars($datos['apellidos']); ?></label>
            <div class="perfil-name2">Huésped</div>
          </div>
        </div>

        <form action="editar_perfil.php" method="POST">

        <div class="form-group">
          <label class="form-label">Documento de Identidad</label>
          <input type="text" class="form-input" value="<?php echo htmlspecialchars($datos['tipo_documento']) . " " . htmlspecialchars($datos['documento']) . " - No editable" ; ?>" disabled>
        </div>
        <div class="form-group">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-input" name="nombre" value="<?php echo htmlspecialchars($datos['nombre']); ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Apellidos</label>
          <input type="text" class="form-input" name="apellidos" value="<?php echo htmlspecialchars($datos['apellidos']); ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Correo Electrónico</label>
          <input type="email" class="form-input" name="correo" value="<?php echo htmlspecialchars($datos['correo']); ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Celular</label>
          <input type="tel" class="form-input" name="celular" value="<?php echo htmlspecialchars($datos['celular']); ?>">
        </div>
        <button type="submit" name="actualizar_datos" class="btn btn-outline" style="margin-top:8px" >Guardar Cambios</button>
        </form>  
        </div>
 
      <div class="perfil-3">
        <div class="perfil-4">
          <div class="perfil-5">Cambiar Contraseña</div>

        <form action="cambiar_contraseña.php" method="POST">

          <div class="form-group">
            <label class="form-label">Contraseña Actual</label>
            <input type="password" name="actual" class="form-input" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label class="form-label">Nueva Contraseña</label>
            <input type="password" name="nueva" class="form-input" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label class="form-label">Confirmar Contraseña</label>
            <input type="password" name="confirmar" class="form-input" placeholder="••••••••">
          </div>
          <button type="submit" name="cambiar_password" class="btn btn-outline">Actualizar</button>
        </div>

        </form>

      </div>
    </section>
  </main>
</body>
</html> 
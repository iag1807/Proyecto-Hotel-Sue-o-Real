<?php
session_start();

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

$current_page    = $_SERVER['PHP_SELF'];
$active_inicio   = ($current_page == '/HotelSueñoReal/cliente.php')      ? ' class="active"' : '';
$active_reservas = ($current_page == '/HotelSueñoReal/mis_reservas.php') ? ' class="active"' : '';
$active_perfil   = ($current_page == '/HotelSueñoReal/perfil.php')       ? ' class="active"' : '';

$habitacionesDisponibles = [
  'sencilla' => [], 'bañera' => [], 'jacuzzi' => [],
  'doble'    => [], 'triple' => [], 'multiple' => [],
];
$busquedaRealizada = false;

$capacidades = [];
$sqlCap = "SELECT tipo_habitacion, MAX(capacidad) as capacidad_max FROM habitaciones GROUP BY tipo_habitacion";
$resultCap = mysqli_query($conn, $sqlCap);
while ($row = mysqli_fetch_assoc($resultCap)) {
  $capacidades[strtolower($row['tipo_habitacion'])] = $row['capacidad_max'];
}

$minimos = [
  'sencilla' => 1, 'bañera' => 1, 'jacuzzi' => 1,
  'doble'    => 2, 'triple' => 3, 'multiple' => 5,
];

function mostrarHabitacion($tipo, $personas, $capacidades, $minimos) {
  if (!$personas) return true;
  if (!isset($capacidades[$tipo]) || !isset($minimos[$tipo])) return true;
  return $personas >= $minimos[$tipo] && $personas <= $capacidades[$tipo];
}

if (isset($_GET['entrada'])) {
  $entrada         = $_GET['entrada'];
  $salida          = $_GET['salida'];
  $numero_personas = $_GET['personas'];
  $busquedaRealizada = true;

  $sql = "SELECT id_habitacion, tipo_habitacion, numero, precio
          FROM habitaciones
          WHERE capacidad >= ?
          AND estado = 'activa'
          AND id_habitacion NOT IN (
            SELECT habitacion_id FROM reservas
            WHERE estado NOT IN ('cancelada')
            AND (
              (? BETWEEN fecha_ingreso AND fecha_salida)
              OR (? BETWEEN fecha_ingreso AND fecha_salida)
              OR (fecha_ingreso BETWEEN ? AND ?)
            )
          )";

  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "issss", $numero_personas, $entrada, $salida, $entrada, $salida);
  mysqli_stmt_execute($stmt);
  $resultado = mysqli_stmt_get_result($stmt);

  while ($row = mysqli_fetch_assoc($resultado)) {
    $tipo  = strtolower($row['tipo_habitacion']);
    $entry = ['id' => $row['id_habitacion'], 'numero' => $row['numero'], 'precio' => $row['precio']];
    if      (strpos($tipo, 'sencilla') !== false) $habitacionesDisponibles['sencilla'][] = $entry;
    elseif  (strpos($tipo, 'bañera')   !== false) $habitacionesDisponibles['bañera'][]   = $entry;
    elseif  (strpos($tipo, 'jacuzzi')  !== false) $habitacionesDisponibles['jacuzzi'][]  = $entry;
    elseif  (strpos($tipo, 'doble')    !== false) $habitacionesDisponibles['doble'][]    = $entry;
    elseif  (strpos($tipo, 'triple')   !== false) $habitacionesDisponibles['triple'][]   = $entry;
    elseif  (strpos($tipo, 'multiple') !== false) $habitacionesDisponibles['multiple'][] = $entry;
  }
  mysqli_stmt_close($stmt);
}

// Mapa habitacion_id -> datos para el modal JS
$mapaHabitaciones = [];
foreach ($habitacionesDisponibles as $tipo => $lista) {
  foreach ($lista as $hab) {
    $mapaHabitaciones[$hab['id']] = [
      'tipo'   => $tipo,
      'numero' => $hab['numero'],
      'precio' => (int)$hab['precio'],
    ];
  }
}

$entradaJS  = isset($entrada)          ? $entrada          : '';
$salidaJS   = isset($salida)           ? $salida           : '';
$personasJS = isset($numero_personas)  ? (int)$numero_personas : 1;
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
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="./imagenes/LogoHotel.png" alt="" class="logo-img">
  </div>
  <ul class="nav-links">
    <li><a href="cliente.php"<?php echo $active_inicio; ?>>
        <span class="icon">&#8962;</span> Inicio
    </a></li>
    <li><a href="mis_reservas.php"<?php echo $active_reservas; ?>>
        <span class="icon">&#9633;</span> Mis reservas
    </a></li>
    <li><a href="perfil.php"<?php echo $active_perfil; ?>>
        <span class="icon">&#937;</span> Mi perfil
    </a></li>
  </ul>
  <div class="sidebar-bottom">
    <a href="iniciodesesion/cerrarsesion.php" class="logout-btn">
      <span>&#9211;</span> Cerrar sesion
    </a>
  </div>
</aside>

<main class="main">

  <nav class="topnav">
    <div class="topnav-right">
      <span class="topnav-date">
        <p id="fecha"></p>
        <script>
          document.getElementById("fecha").innerHTML =
            new Date().toLocaleDateString('es-ES', {year:'numeric',month:'long',day:'numeric'});
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
          <em><?php echo htmlspecialchars($datos['nombre']).' '.htmlspecialchars($datos['apellidos']); ?></em>
        </h2>
      </div>
    </div>
  </section>

  <h1 class="section-title">BUSCAR DISPONIBILIDAD</h1>

  <form id="form-busqueda" method="GET" action="cliente.php">
    <div class="buscador-hotel">

      <div class="campo" id="campo-ingreso">
        <label>Fecha de entrada</label>
        <div class="date-display empty" id="display-ingreso">
          <span id="txt-ingreso">dd/mm/aaaa</span>
        </div>
        <input type="hidden" id="fecha_ingreso" name="entrada"
               value="<?php echo htmlspecialchars($entrada ?? ''); ?>" required>
        <div class="cal-popup" id="cal-ingreso"></div>
      </div>

      <div class="linea"></div>

      <div class="campo" id="campo-salida">
        <label>Fecha de salida</label>
        <div class="date-display empty" id="display-salida">
          <span id="txt-salida">dd/mm/aaaa</span>
        </div>
        <input type="hidden" id="fecha_salida" name="salida"
               value="<?php echo htmlspecialchars($salida ?? ''); ?>" required>
        <div class="cal-popup" id="cal-salida"></div>
      </div>

      <script>
        function parseLocalDateFromISO(iso) {
          if (!iso) return null;
          const [y, m, d] = iso.split('-').map(Number);
          if (isNaN(y)||isNaN(m)||isNaN(d)) return null;
          return new Date(y, m-1, d);
        }
        function setDisplayDate(displayId, textId, iso) {
          const display = document.getElementById(displayId);
          const text    = document.getElementById(textId);
          const date    = parseLocalDateFromISO(iso);
          if (date) {
            display.classList.remove('empty');
            text.textContent = date.toLocaleDateString('es-ES',{year:'numeric',month:'2-digit',day:'2-digit'});
            return;
          }
          display.classList.add('empty');
          text.textContent = 'dd/mm/aaaa';
        }
        <?php if (!empty($entrada)): ?>
          setDisplayDate('display-ingreso','txt-ingreso',"<?php echo $entrada; ?>");
        <?php endif; ?>
        <?php if (!empty($salida)): ?>
          setDisplayDate('display-salida','txt-salida',"<?php echo $salida; ?>");
        <?php endif; ?>
      </script>

      <div class="linea"></div>

      <div class="campo">
        <label>Numero de personas</label>
        <input type="number" id="numero_personas" name="personas"
               placeholder="huespedes" min="1" max="10"
               value="<?php echo htmlspecialchars($numero_personas ?? ''); ?>" required>
      </div>

      <div class="linea"></div>

      <button type="submit" class="btn-buscar">Buscar</button>
    </div>
  </form>

  <p class="resultado-busqueda">HABITACIONES DISPONIBLES</p>

  <section class="habitaciones-section">
    <div class="habitaciones-grid">

<?php
$tarjetas = [
  'sencilla' => ['img'=>'sencilla.jpeg',  'nombre'=>'Sencilla',  'desc'=>'Cama semidoble, baño privado, televisor',                               'cap'=>'1 a 2 personas',  'precio'=>'Desde $50.000'],
  'bañera'   => ['img'=>'bañera.jpeg',    'nombre'=>'Bañera',    'desc'=>'Cama semidoble, baño privado, banera, televisor',                       'cap'=>'1 a 2 personas',  'precio'=>'$130.000'],
  'jacuzzi'  => ['img'=>'jacuzzi.jpeg',   'nombre'=>'Jacuzzi',   'desc'=>'Cama semidoble, baño privado, jacuzzi, televisor',                      'cap'=>'1 a 2 personas',  'precio'=>'$160.000'],
  'doble'    => ['img'=>'doble.jpeg',     'nombre'=>'Doble',     'desc'=>'Dos camas semidobles, baño privado, televisor',                         'cap'=>'2 a 4 personas',  'precio'=>'$110.000'],
  'triple'   => ['img'=>'triple.jpeg',    'nombre'=>'Triple',    'desc'=>'Una cama semidoble, un camarote, baño privado, televisor',              'cap'=>'3 a 6 personas',  'precio'=>'$130.000'],
  'multiple' => ['img'=>'multiple.jpeg',  'nombre'=>'Multiple',  'desc'=>'Dos camarotes, una cama de un metro, baño privado, televisor',          'cap'=>'5 a 10 personas', 'precio'=>'$160.000'],
];


// Mapeo key -> clave en habitacionesDisponibles
$keyMap = [
  'sencilla'=>'sencilla','bañera'=>'bañera','jacuzzi'=>'jacuzzi',
  'doble'=>'doble','triple'=>'triple','multiple'=>'multiple'
];

foreach ($tarjetas as $key => $info):
  $tipoKey = $keyMap[$key];
  if (!mostrarHabitacion($tipoKey, $numero_personas ?? null, $capacidades, $minimos)) continue;
?>
      <div class="habitacion-card">
        <img class="habitacion-imagen" src="imagenes/<?php echo $info['img']; ?>" alt="<?php echo $key; ?>">
        <div class="habitacion-content">
          <h3 class="habitacion-nombre"><?php echo $info['nombre']; ?></h3>
          <p class="habitacion-detalles"><?php echo $info['desc']; ?></p>
          <p class="habitacion-detalles">Capacidad: <?php echo $info['cap']; ?></p>
          <p class="habitacion-detalles">Precio: <?php echo $info['precio']; ?> por noche</p>
          <?php if ($busquedaRealizada): ?>
            <p class="habitacion-detalles">Disponibles:</p>
            <?php if (empty($habitacionesDisponibles[$tipoKey])): ?>
              <p class="habitacion-detalles sin-disponibles">No hay habitaciones disponibles</p>
            <?php else: ?>
              <select class="habitacion-select" onchange="abrirModalReserva(this.value)">
                <option value="">Habitacion</option>
                <?php foreach ($habitacionesDisponibles[$tipoKey] as $hab): ?>
                  <option value="<?php echo $hab['id']; ?>">N&deg; <?php echo $hab['numero']; ?></option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
<?php endforeach; ?>

    </div>
  </section>
</main>

<!-- Datos PHP -> JS -->
<script>
  const mapaHabitaciones = <?php echo json_encode($mapaHabitaciones); ?>;
  const reservaEntrada   = "<?php echo $entradaJS; ?>";
  const reservaSalida    = "<?php echo $salidaJS; ?>";
  const reservaPersonas  = <?php echo $personasJS; ?>;
</script>

<!-- MODAL -->
<div id="modal-reserva" class="modal-overlay" style="display:none;">
  <div class="modal-container">

    <button class="modal-close" onclick="cerrarModal()">&#10005;</button>

    <div class="modal-header">
      <h2 class="modal-titulo">Confirmar Reserva</h2>
      <p class="modal-subtitulo">Hotel Sueno Real</p>
    </div>

    <div class="modal-resumen" id="bloque-resumen">
      <h3 class="resumen-title">Resumen</h3>
      <div class="resumen-grid">
        <div class="resumen-item"><span class="resumen-label">Habitacion</span><span class="resumen-valor" id="res-tipo">-</span></div>
        <div class="resumen-item"><span class="resumen-label">N. Habitacion</span><span class="resumen-valor" id="res-numero">-</span></div>
        <div class="resumen-item"><span class="resumen-label">Noches</span><span class="resumen-valor" id="res-noches">-</span></div>
        <div class="resumen-item"><span class="resumen-label">Entrada</span><span class="resumen-valor" id="res-entrada">-</span></div>
        <div class="resumen-item"><span class="resumen-label">Salida</span><span class="resumen-valor" id="res-salida">-</span></div>
        <div class="resumen-item"><span class="resumen-label">Huespedes</span><span class="resumen-valor" id="res-personas">-</span></div>
      </div>
      <div class="resumen-totales">
        <div class="total-row"><span>Precio por noche</span><span id="res-precio-noche">-</span></div>
        <div class="total-row total-full"><span>Total estadia</span><span id="res-total">-</span></div>
        <div class="total-row total-anticipo highlight"><span>Anticipo a pagar (50%)</span><span id="res-anticipo">-</span></div>
      </div>
      <div class="info-pago-nota">
        <h3 class="resumen-title">Tener en cuenta</h3>
        • Solo debes pagar el <strong>50%</strong> para reservar la habitacion. <br>
        • Una vez revisado el comprobante se confirmara tu reserva. <br>
        • El saldo restante se cancela al momento del ingreso en el hotel.
      </div>
    </div>

    <div class="modal-pago" id="bloque-pago">
      <h3 class="resumen-title">Pago del anticipo</h3>
      <div class="pago-grid">

        <div class="pago-card">
          <div class="pago-card-header">
            <span class="pago-badge bancolombia">Bancolombia</span>
            <p class="pago-instruccion">Escanea el QR para realizar la transferencia</p>
            <p class="pago-instruccion">Nombre: Martha Cecilia Carvajal Bran </p>
            <p class="pago-instruccion">Cuenta: 64759538917 - Ahorros</p>
          </div>
          <div class="qr-container">
            <img src="imagenes/qr.jpeg" alt="QR Bancolombia" class="qr-img"
                 onerror="this.style.display='none';document.getElementById('qr-placeholder').style.display='flex';">
          </div>
          <p class="pago-monto-label">Precio a transferir: <strong id="qr-monto">-</strong></p>
        </div>

        <div class="pago-card">
          <div class="pago-card-header">
            <span class="pago-badge comprobante">Comprobante</span>
            <p class="pago-instruccion">Adjunta la foto o captura de tu pago</p>
          </div>
          <div class="upload-area" id="upload-area" onclick="document.getElementById('comprobante-input').click()">
            <div class="upload-icon">&#128206;</div>
            <p class="upload-text">Toca para subir el comprobante</p>
            <input type="file" id="comprobante-input" accept="image/*,application/pdf"
                   style="display:none;" onchange="previsualizarComprobante(event)">
          </div>
          <div id="preview-container" style="display:none;flex-direction:column;gap:10px;">
            <img id="preview-img" class="preview-img" alt="Vista previa">
            <button class="btn-cambiar-archivo" onclick="document.getElementById('comprobante-input').click()">
              Cambiar archivo
            </button>
          </div>
        </div>

      </div>
    </div>

    <div class="modal-footer" id="bloque-footer">
      <button class="btn-confirmar" id="btn-confirmar" onclick="confirmarReserva()">
        <span id="btn-texto">Confirmar Reserva</span>
        <span id="btn-loading" style="display:none;">Procesando...</span>
      </button>
      <button class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
    </div>

    <div id="reserva-exitosa" class="reserva-exitosa" style="display:none;">
      <h3>Reserva registrada</h3>
      <p>Tu reserva ha sido registrada exitosamente.<br>
         Se revisara el comprobante y se confirmara tu reserva.<br>
         Recuerda presentarte con tu documento de identidad al hacer el check-in.</p>

         <button class="btn-reservas"><a href="mis_reservas.php">Ver reservas</a></button>
    </div>

  </div>
</div>

<style>
.modal-overlay {
  position:fixed;inset:0;background:rgba(0,0,0,.78);
  backdrop-filter:blur(5px);z-index:9999;
  display:flex;align-items:center;justify-content:center;
  padding:16px;animation:mFadeIn .25s ease;
}
@keyframes mFadeIn  {from{opacity:0}to{opacity:1}}
@keyframes mSlideUp {from{transform:translateY(28px);opacity:0}to{transform:translateY(0);opacity:1}}

.modal-container {
  background:rgba(0,0,0,.78);border:1px solid #c9a84c1a;border-radius:18px;
  width:100%;max-width:760px;max-height:90vh;overflow-y:auto;
  padding:36px 36px 28px;position:relative;animation:mSlideUp .32s ease;
  scrollbar-width:thin;scrollbar-color:#c9a84c33 transparent;
}
.modal-container::-webkit-scrollbar{width:5px}
.modal-container::-webkit-scrollbar-thumb{background:#c9a84c44;border-radius:3px}

.modal-close {
  position:absolute;top:16px;right:20px;background:transparent;
  border:none;color:#777;font-size:18px;cursor:pointer;transition:color .2s;line-height:1;
}
.modal-close:hover{color:#c9a84c}

.modal-header{text-align:center;margin-bottom:28px}
.modal-titulo{font-family:'Cormorant Garamond',serif;color:#c9a96e;font-size:28px;font-weight:600;letter-spacing:2px;margin:0 0 4px}
.modal-subtitulo{color:#777;font-size:13px;letter-spacing:1px;margin:0}

.modal-resumen,.modal-pago{background: #000;border:1px solid #c9a84c1a;border-radius:12px;padding:24px;margin-bottom:20px}
.resumen-title{font-family:'Cormorant Garamond',serif;color: #c9a96c;font-size:15px;letter-spacing:2px;text-transform:uppercase;margin:0 0 18px;font-weight:400}
.resumen-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px}
@media(max-width:520px){.resumen-grid{grid-template-columns:repeat(2,1fr)}}
.resumen-item{display:flex;flex-direction:column;gap:4px}
.resumen-label{font-size:11px;color:#555;letter-spacing:1px;text-transform:uppercase}
.resumen-valor{font-family: 'Times New Roman', Times, serif;font-size:16px;color:#c9a96c;font-weight:400}

.resumen-totales{border-top:1px solid #c9a84c18;padding-top:16px;display:flex;flex-direction:column;gap:10px}
.total-row{display:flex;justify-content:space-between;align-items:center;font-size:14px;color:#999}
.total-row span:last-child{font-family: 'Times New Roman', Times, serif;font-size:14px;color:#ddd}
.total-full span:last-child{color: #c9a96c;font-size:14px;font-weight:600}
.highlight{border:1px solid #c9a84c1a;border-radius:8px;padding:10px 14px;color: #c9a96c!important;font-weight:400}
.highlight span:last-child{color: #c9a96c!important;font-size:23px!important}
.total-resta span:last-child{color:#777}

.info-pago-nota{margin-top:16px;border:2px solid #c9a84c1a;padding:12px 16px;font-size:13px;color:#999;border-radius:0 8px 8px 0;line-height:1.7}

.pago-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:560px){.pago-grid{grid-template-columns:1fr}}
.pago-card{border:1px solid #c9a84c1a;border-radius:12px;padding:18px;display:flex;flex-direction:column;gap:14px}
.pago-card-header{display:flex;flex-direction:column;gap:6px}
.pago-badge{display:inline-block;padding:3px 12px;border-radius:20px;font-size:11px;letter-spacing:1px;text-transform:uppercase;font-weight:700;width:fit-content}
.pago-badge.bancolombia, .pago-badge.comprobante{background:#c9a84c18;color:#c9a84c;border:1px solid #c9a84c44}
.pago-instruccion{font-size:12px;color:#777;margin:0}

.qr-container{border-radius:10px;padding:12px;display:flex;align-items:center;justify-content:center;min-height:160px}
.qr-img{width:100%;;height:auto;display:block;margin:auto}
.pago-monto-label{font-size:13px;color:#aaa;text-align:center;margin:0}
.pago-monto-label strong{color: #c9a96c;font-size:16px}

.upload-area{border:2px dashed #c9a84c33;border-radius:10px;padding:28px 16px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;min-height:260px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px}
.upload-area:hover{border-color:#c9a84c;background:#c9a84c08}
.upload-icon{font-size:28px}
.upload-text{font-size:13px;color:#ccc;margin:0}
.upload-hint{font-size:11px;color:#555;margin:0}
.preview-img{width:100%;max-height:160px;object-fit:contain;border-radius:8px;border:1px solid #c9a84c33}
.btn-cambiar-archivo{background:transparent;border:1px solid #c9a84c44;color:#c9a84c;padding:6px 14px;border-radius:6px;font-size:12px;cursor:pointer;transition:background .2s;width:100%}
.btn-cambiar-archivo:hover{background:#c9a84c15}

.modal-footer{display:flex;flex-direction:column;gap:10px}
.btn-confirmar{width:100%;padding:16px;background:#c9a84c;color:#1a1000;border:none;border-radius:10px;font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;letter-spacing:2px;cursor:pointer;transition:opacity .2s,transform .15s}
.btn-confirmar:hover:not(:disabled){opacity:.9;transform:translateY(-2px)}
.btn-confirmar:disabled{opacity:.45;cursor:not-allowed;transform:none}
.btn-cancelar{width:100%;padding:12px;background:transparent;color:#555;border:1px solid #2a2a2a;border-radius:10px;font-size:14px;cursor:pointer;transition:color .2s,border-color .2s}
.btn-cancelar:hover{color:#999;border-color:#444}

.reserva-exitosa{text-align:center;padding:44px 20px 20px}
.reserva-exitosa h3{font-family:'Cormorant Garamond',serif;color: #c9a96c;font-size:30px;margin:0 0 8px}
.reserva-exitosa p{color:#999;line-height:1.8;margin:0}
.btn-reservas{margin-top:70px;padding:12px 20px;background:#c9a96c;color:#000;border:none;border-radius:8px;font-size:14px;cursor:pointer;transition:background .2s}
.btn-reservas a{color:#000;text-decoration:none}
</style>

<script>
let habitacionSeleccionadaId = null;
let comprobanteBase64        = null;
let comprobanteNombre        = null;

function formatPeso(n) {
  return '$' + Number(n).toLocaleString('es-CO');
}
function formatFecha(iso) {
  if (!iso) return '-';
  const [y,m,d] = iso.split('-');
  return d+'/'+m+'/'+y;
}
function calcNoches(entrada, salida) {
  return Math.round((new Date(salida+'T00:00:00') - new Date(entrada+'T00:00:00')) / 86400000);
}

function abrirModalReserva(idHabitacion) {
  if (!idHabitacion) return;
  const hab = mapaHabitaciones[idHabitacion];
  if (!hab) return;

  habitacionSeleccionadaId = idHabitacion;
  const noches   = calcNoches(reservaEntrada, reservaSalida);
  const total    = hab.precio * noches;
  const anticipo = Math.round(total * 0.5);
  const saldo    = total - anticipo;
  const tipoLabel = hab.tipo.charAt(0).toUpperCase() + hab.tipo.slice(1);

  document.getElementById('res-tipo').textContent         = tipoLabel;
  document.getElementById('res-numero').textContent       = 'N\u00b0 ' + hab.numero;
  document.getElementById('res-entrada').textContent      = formatFecha(reservaEntrada);
  document.getElementById('res-salida').textContent       = formatFecha(reservaSalida);
  document.getElementById('res-noches').textContent       = noches + (noches===1?' noche':' noches');
  document.getElementById('res-personas').textContent     = reservaPersonas + (reservaPersonas==1?' persona':' personas');
  document.getElementById('res-precio-noche').textContent = formatPeso(hab.precio);
  document.getElementById('res-total').textContent        = formatPeso(total);
  document.getElementById('res-anticipo').textContent     = formatPeso(anticipo);
  document.getElementById('qr-monto').textContent         = formatPeso(anticipo);

  comprobanteBase64 = null; comprobanteNombre = null;
  document.getElementById('comprobante-input').value         = '';
  document.getElementById('preview-container').style.display = 'none';
  document.getElementById('upload-area').style.display       = 'flex';
  document.getElementById('reserva-exitosa').style.display   = 'none';
  document.getElementById('bloque-resumen').style.display    = 'block';
  document.getElementById('bloque-pago').style.display       = 'block';
  document.getElementById('bloque-footer').style.display     = 'flex';

  const btn = document.getElementById('btn-confirmar');
  btn.disabled = false;
  document.getElementById('btn-texto').style.display   = 'inline';
  document.getElementById('btn-loading').style.display = 'none';

  document.getElementById('modal-reserva').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function cerrarModal() {
  document.getElementById('modal-reserva').style.display = 'none';
  document.body.style.overflow = '';
  document.querySelectorAll('.habitacion-select').forEach(s => s.value = '');
}

document.getElementById('modal-reserva').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});

function previsualizarComprobante(event) {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) { alert('El archivo no puede superar 5MB.'); return; }
  comprobanteNombre = file.name;
  const reader = new FileReader();
  reader.onload = function(e) {
    comprobanteBase64 = e.target.result;
    document.getElementById('preview-img').src = file.type.startsWith('image/') ? comprobanteBase64 : '';
    document.getElementById('preview-img').alt = file.name;
    document.getElementById('preview-container').style.display = 'flex';
    document.getElementById('upload-area').style.display       = 'none';
  };
  reader.readAsDataURL(file);
}

async function confirmarReserva() {
  if (!comprobanteBase64) {
    alert('Por favor adjunta el comprobante de pago antes de confirmar.');
    return;
  }
  const btn     = document.getElementById('btn-confirmar');
  const txtNorm = document.getElementById('btn-texto');
  const txtLoad = document.getElementById('btn-loading');
  btn.disabled = true;
  txtNorm.style.display = 'none';
  txtLoad.style.display = 'inline';

  try {
    const resp = await fetch('procesar_reserva.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({
        id_habitacion : habitacionSeleccionadaId,
        fecha_ingreso : reservaEntrada,
        fecha_salida  : reservaSalida,
        num_personas  : reservaPersonas,
        comprobante   : comprobanteBase64,
        nombre_archivo: comprobanteNombre,
      }),
    });
    const data = await resp.json();
    if (data.exito) {
      document.getElementById('bloque-resumen').style.display  = 'none';
      document.getElementById('bloque-pago').style.display     = 'none';
      document.getElementById('bloque-footer').style.display   = 'none';
      document.getElementById('reserva-exitosa').style.display = 'block';
    } else {
      alert('Error: ' + (data.mensaje || 'Intenta de nuevo.'));
      btn.disabled = false;
      txtNorm.style.display = 'inline';
      txtLoad.style.display = 'none';
    }
  } catch(err) {
    alert('Error de conexion. Verifica tu internet e intenta de nuevo.');
    btn.disabled = false;
    txtNorm.style.display = 'inline';
    txtLoad.style.display = 'none';
  }
}
</script>

<script src="calendario.js"></script>
</body>
</html>
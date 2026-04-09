<?php
/*
 * procesar_reserva.php
 * Recibe la reserva en JSON desde el modal del cliente y la guarda en la BD.
 * Guarda el comprobante como archivo en el servidor.
 *
 * Columnas de la tabla `reservas`:
 *   id_reserva        INT AUTO_INCREMENT PK
 *   cliente_documento INT(11)
 *   habitacion_id     INT(11)
 *   fecha_ingreso     DATE
 *   fecha_salida      DATE
 *   numero_personas   INT(11)
 *   total             DECIMAL(10,2)
 *   anticipo          DECIMAL(10,2)
 *   estado            ENUM('pendiente','confirmada','cancelada','finalizada') DEFAULT 'pendiente'
 *   comprobante_pago  VARCHAR(255) NULL
 *   fecha_creacion    DATETIME DEFAULT current_timestamp()
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
  echo json_encode(['exito' => false, 'mensaje' => 'Sesión no válida.']);
  exit();
}

require_once 'conexion.php';

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data) {
  echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos.']);
  exit();
}

$id_habitacion   = isset($data['id_habitacion'])  ? intval($data['id_habitacion'])  : 0;
$fecha_ingreso   = isset($data['fecha_ingreso'])  ? trim($data['fecha_ingreso'])     : '';
$fecha_salida    = isset($data['fecha_salida'])   ? trim($data['fecha_salida'])      : '';
$num_personas    = isset($data['num_personas'])   ? intval($data['num_personas'])    : 0;
$comprobante_b64 = isset($data['comprobante'])    ? $data['comprobante']             : '';

if (!$id_habitacion || !$fecha_ingreso || !$fecha_salida || !$num_personas || !$comprobante_b64) {
  echo json_encode(['exito' => false, 'mensaje' => 'Faltan datos requeridos.']);
  exit();
}

// Validar fechas
$hoy     = new DateTime('today');
$entrada = DateTime::createFromFormat('Y-m-d', $fecha_ingreso);
$salida  = DateTime::createFromFormat('Y-m-d', $fecha_salida);

if (!$entrada || !$salida || $entrada < $hoy || $salida <= $entrada) {
  echo json_encode(['exito' => false, 'mensaje' => 'Las fechas no son válidas.']);
  exit();
}

// ── Guardar comprobante en disco ──────────────────────────────────────────────
$dirComprobantes = __DIR__ . '/comprobantes/';
if (!is_dir($dirComprobantes)) {
  mkdir($dirComprobantes, 0755, true);
}

$base64Data = $comprobante_b64;
if (strpos($base64Data, ',') !== false) {
  $base64Data = explode(',', $base64Data, 2)[1];
}

$ext = 'jpg';
if (strpos($comprobante_b64, 'image/png')           !== false) $ext = 'png';
elseif (strpos($comprobante_b64, 'image/jpeg')      !== false) $ext = 'jpg';
elseif (strpos($comprobante_b64, 'application/pdf') !== false) $ext = 'pdf';

$nombreUnico  = 'comp_' . $_SESSION['documento'] . '_' . time() . '.' . $ext;
$rutaArchivo  = $dirComprobantes . $nombreUnico;
$rutaRelativa = 'comprobantes/' . $nombreUnico;

$guardado = file_put_contents($rutaArchivo, base64_decode($base64Data));

if ($guardado === false) {
  echo json_encode(['exito' => false, 'mensaje' => 'No se pudo guardar el comprobante en el servidor.']);
  exit();
}

// ── Obtener precio de la habitación ──────────────────────────────────────────
$sqlPrecio = "SELECT precio FROM habitaciones WHERE id_habitacion = ? AND estado = 'activa'";
$stmtP = mysqli_prepare($conn, $sqlPrecio);
mysqli_stmt_bind_param($stmtP, 'i', $id_habitacion);
mysqli_stmt_execute($stmtP);
$resP     = mysqli_stmt_get_result($stmtP);
$habDatos = mysqli_fetch_assoc($resP);
mysqli_stmt_close($stmtP);

if (!$habDatos) {
  unlink($rutaArchivo);
  echo json_encode(['exito' => false, 'mensaje' => 'La habitación no está disponible.']);
  exit();
}

$precioNoche  = floatval($habDatos['precio']);
$noches       = $entrada->diff($salida)->days;
$totalReserva = $precioNoche * $noches;
$anticipo     = round($totalReserva * 0.5, 2);

// ── Verificar que la habitación sigue libre ───────────────────────────────────
$sqlVerif = "SELECT id_reserva FROM reservas
             WHERE habitacion_id = ?
             AND estado NOT IN ('cancelada')
             AND (
               (? BETWEEN fecha_ingreso AND fecha_salida)
               OR (? BETWEEN fecha_ingreso AND fecha_salida)
               OR (fecha_ingreso BETWEEN ? AND ?)
             )
             LIMIT 1";
$stmtV = mysqli_prepare($conn, $sqlVerif);
mysqli_stmt_bind_param($stmtV, 'issss',
  $id_habitacion,
  $fecha_ingreso,
  $fecha_salida,
  $fecha_ingreso,
  $fecha_salida
);
mysqli_stmt_execute($stmtV);
mysqli_stmt_store_result($stmtV);

if (mysqli_stmt_num_rows($stmtV) > 0) {
  mysqli_stmt_close($stmtV);
  unlink($rutaArchivo);
  echo json_encode(['exito' => false, 'mensaje' => 'La habitación ya no está disponible para esas fechas. Por favor elige otra.']);
  exit();
}
mysqli_stmt_close($stmtV);

// ── Insertar la reserva ───────────────────────────────────────────────────────
$sqlInsert = "INSERT INTO reservas
                (cliente_documento, habitacion_id, fecha_ingreso, fecha_salida,
                 numero_personas, total, anticipo, comprobante_pago, estado)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";

$stmtI = mysqli_prepare($conn, $sqlInsert);

if (!$stmtI) {
  unlink($rutaArchivo);
  echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . mysqli_error($conn)]);
  exit();
}

// i=cliente_documento, i=habitacion_id, s=fecha_ingreso, s=fecha_salida,
// i=numero_personas,   d=total,         d=anticipo,      s=comprobante_pago
mysqli_stmt_bind_param(
  $stmtI,
  'iissidds',
  $_SESSION['documento'],
  $id_habitacion,
  $fecha_ingreso,
  $fecha_salida,
  $num_personas,
  $totalReserva,
  $anticipo,
  $rutaRelativa
);

$ok = mysqli_stmt_execute($stmtI);

if (!$ok) {
  $error = mysqli_stmt_error($stmtI);
  mysqli_stmt_close($stmtI);
  unlink($rutaArchivo);
  echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar la reserva: ' . $error]);
  exit();
}

$idReserva = mysqli_insert_id($conn);
mysqli_stmt_close($stmtI);

echo json_encode([
  'exito'      => true,
  'id_reserva' => $idReserva,
  'mensaje'    => 'Reserva registrada correctamente.',
]);
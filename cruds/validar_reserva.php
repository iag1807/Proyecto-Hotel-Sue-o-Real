<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registrar_reserva.php');
    exit;
}

$cliente_documento = trim($_POST['cliente_documento'] ?? '');
$habitacion_id     = (int)($_POST['habitacion_id']     ?? 0);
$fecha_ingreso     = trim($_POST['fecha_ingreso']      ?? '');
$fecha_salida      = trim($_POST['fecha_salida']       ?? '');
$numero_personas   = (int)($_POST['numero_personas']   ?? 0);
$estado            = trim($_POST['estado']             ?? '');

$errores = [];

if (empty($cliente_documento)) {
    $errores[] = 'El cliente es obligatorio.';
}

if ($habitacion_id <= 0) {
    $errores[] = 'Debe seleccionar una habitación.';
}

if (empty($fecha_ingreso)) {
    $errores[] = 'La fecha de ingreso es obligatoria.';
} 

elseif ($fecha_ingreso < date('Y-m-d')) {
    $errores[] = 'La fecha de ingreso no puede ser anterior a hoy.';
}

if (empty($fecha_salida)) {
    $errores[] = 'La fecha de salida es obligatoria.';
}

if (!empty($fecha_ingreso) && !empty($fecha_salida) && $fecha_salida <= $fecha_ingreso) {
    $errores[] = 'La fecha de salida debe ser posterior a la de ingreso.';
}

if ($numero_personas <= 0) {
    $errores[] = 'El número de personas debe ser al menos 1.';
}


$estados_validos = ['confirmada', 'cancelada', 'finalizada'];
if (empty($estado) || !in_array($estado, $estados_validos)) {
    $errores[] = 'El estado seleccionado no es válido.';
}


if (empty($errores)) {

    $stmt = $conn->prepare("SELECT documento FROM clientes WHERE documento = ? AND estado = 'activo' LIMIT 1");
    $stmt->bind_param('i', $cliente_documento);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $errores[] = 'El cliente seleccionado no está activo.';
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT capacidad, precio FROM habitaciones WHERE id_habitacion = ? AND estado = 'activa' LIMIT 1");
    $stmt->bind_param('i', $habitacion_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $hab = $res->fetch_assoc();
    $stmt->close();

    if (!$hab) {
        $errores[] = 'La habitación seleccionada no existe o no está disponible.';
    } else {
    
        if ($numero_personas > $hab['capacidad']) {
            $errores[] = "La habitación tiene capacidad máxima de {$hab['capacidad']} personas.";
        }

        
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS cnt
            FROM reservas
            WHERE habitacion_id = ?
              AND estado IN ('pendiente', 'confirmada')
              AND fecha_ingreso  < ?
              AND fecha_salida   > ?
        ");
        $stmt->bind_param('iss', $habitacion_id, $fecha_salida, $fecha_ingreso);
        $stmt->execute();
        $cnt = $stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        if ($cnt > 0) {
            $errores[] = 'La habitación no está disponible en las fechas seleccionadas.';
        }

        
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS cnt
            FROM disponibilidad
            WHERE habitacion_id = ?
              AND fecha >= ?
              AND fecha <  ?
        ");
        $stmt->bind_param('iss', $habitacion_id, $fecha_ingreso, $fecha_salida);
        $stmt->execute();
        $cnt2 = $stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        if ($cnt2 > 0) {
            $errores[] = 'La habitación tiene días bloqueados en las fechas seleccionadas.';
        }
    }
}


if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = [
        'cliente_documento' => $cliente_documento,
        'habitacion_id'     => $habitacion_id,
        'fecha_ingreso'     => $fecha_ingreso,
        'fecha_salida'      => $fecha_salida,
        'numero_personas'   => $numero_personas,
        'estado'            => $estado,
    ];
    header('Location: registrar_reserva.php');
    exit;
}


$stmt = $conn->prepare("
    INSERT INTO reservas (cliente_documento, habitacion_id, fecha_ingreso, fecha_salida, numero_personas, estado)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param('iissis',
    $cliente_documento,
    $habitacion_id,
    $fecha_ingreso,
    $fecha_salida,
    $numero_personas,
    $estado
);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['exito'] = 'Reserva registrada exitosamente.';
    header('Location: ver_reservas.php');
    exit;
} else {
    
    @unlink($destino);
    $stmt->close();
    $_SESSION['errores'] = ['Error al guardar la reserva en la base de datos. Intenta de nuevo.'];
    $_SESSION['old'] = [
        'cliente_documento' => $cliente_documento,
        'habitacion_id'     => $habitacion_id,
        'fecha_ingreso'     => $fecha_ingreso,
        'fecha_salida'      => $fecha_salida,
        'numero_personas'   => $numero_personas,
        'estado'            => $estado,
    ];
    header('Location: registrar_reserva.php');
    exit;
}
?>
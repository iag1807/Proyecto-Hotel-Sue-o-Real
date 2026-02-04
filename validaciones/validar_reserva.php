<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: ../reserva.php");
    exit();
}

require_once '../conexion.php';

$fecha_ingreso = new DateTime($_POST['fecha_ingreso']);
$fecha_salida = new DateTime($_POST['fecha_salida']);
$numero_personas = $_POST['numero_personas'];
$habitacion_numero = $_POST['habitacion_numero'];
$cliente_documento = $_SESSION['documento'];
$hoy = new DateTime();

$errores = [];

$hoy->setTime(0, 0, 0);

if (empty($_POST['fecha_ingreso']) || empty($_POST['fecha_salida']) || empty($numero_personas) || empty($habitacion_numero)) {
    $errores[] = "La reserva está incompleta.";
} elseif ($fecha_ingreso > $fecha_salida) {
    $errores[] = "La fecha de salida debe ser mayor que la de ingreso.";
} elseif ($fecha_ingreso < $hoy) {
    $errores[] = "La fecha de ingreso no puede ser anterior a hoy.";
} elseif (!filter_var($numero_personas, FILTER_VALIDATE_INT)) {
    $errores[] = "El número de personas debe ser un número entero.";
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("location: ../reserva.php");
    exit();
}

$sql_estado = "SELECT estado FROM habitaciones WHERE numero = ?";
$stmt_estado = mysqli_prepare($conn, $sql_estado);
mysqli_stmt_bind_param($stmt_estado, "i", $habitacion_numero);
mysqli_stmt_execute($stmt_estado);
mysqli_stmt_bind_result($stmt_estado, $estado_habitacion);
mysqli_stmt_fetch($stmt_estado);
mysqli_stmt_close($stmt_estado);

if ($estado_habitacion === null) {
    $errores[] = "La habitación seleccionada no existe.";
} elseif ($estado_habitacion === 'mantenimiento') {
    $errores[] = "La habitación está en mantenimiento y no se puede reservar.";
}

if (empty($errores)) {
    $fecha_ingreso_sql = $fecha_ingreso->format('Y-m-d H:i:s');
    $fecha_salida_sql  = $fecha_salida->format('Y-m-d H:i:s');

    $sql_reserva = "SELECT COUNT(*) 
                    FROM reservas 
                    WHERE habitacion_numero = ?
                    AND fecha_salida > NOW()
                    AND (
                        (? BETWEEN fecha_ingreso AND fecha_salida)
                        OR (? BETWEEN fecha_ingreso AND fecha_salida)
                        OR (fecha_ingreso BETWEEN ? AND ?)
                        OR (fecha_salida BETWEEN ? AND ?)
                    )";

    $stmt_reserva = mysqli_prepare($conn, $sql_reserva);
    mysqli_stmt_bind_param(
        $stmt_reserva,
        "issssss",
        $habitacion_numero,
        $fecha_ingreso_sql,
        $fecha_salida_sql,
        $fecha_ingreso_sql,
        $fecha_salida_sql,
        $fecha_ingreso_sql,
        $fecha_salida_sql
    );
    mysqli_stmt_execute($stmt_reserva);
    mysqli_stmt_bind_result($stmt_reserva, $reservas_existentes);
    mysqli_stmt_fetch($stmt_reserva);
    mysqli_stmt_close($stmt_reserva);

    if ($reservas_existentes > 0) {
        $errores[] = "La habitación ya tiene una reserva activa o cruzada en esas fechas.";
    }
}


if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    header("location: ../reserva.php");
    exit();
}

$sql = "INSERT INTO reservas (fecha_ingreso, fecha_salida, numero_personas, habitacion_numero, cliente_documento) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "ssiii",
    $fecha_ingreso_sql,
    $fecha_salida_sql,
    $numero_personas,
    $habitacion_numero,
    $cliente_documento
);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


$update_sql = "UPDATE habitaciones SET estado = 'ocupada' WHERE numero = ?";
$stmt_update = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt_update, "i", $habitacion_numero);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);


$_SESSION['success'] = "Reserva registrada exitosamente.";
header("Location: ../reserva.php");
exit();

?>

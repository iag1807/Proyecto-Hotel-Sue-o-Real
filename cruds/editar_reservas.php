<?php
session_start();
require_once '../conexion.php';


$id_reserva = (int)($_GET['id_reserva'] ?? 0);

if ($id_reserva <= 0) {
    header('Location: ver_reservas.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT r.*, h.capacidad, h.precio
    FROM reservas r
    JOIN habitaciones h ON r.habitacion_id = h.id_habitacion
    WHERE r.id_reserva = ?
    LIMIT 1
");
$stmt->bind_param('i', $id_reserva);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reserva) {
    header('Location: ver_reservas.php');
    exit;
}


$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cliente_documento = trim($_POST['cliente_documento'] ?? '');
    $habitacion_id     = (int)($_POST['habitacion_id']     ?? 0);
    $fecha_ingreso     = trim($_POST['fecha_ingreso']      ?? '');
    $fecha_salida      = trim($_POST['fecha_salida']       ?? '');
    $numero_personas   = (int)($_POST['numero_personas']   ?? 0);
    $estado            = trim($_POST['estado']             ?? '');


    if (empty($cliente_documento))  $errores[] = 'El cliente es obligatorio.';
    if ($habitacion_id <= 0)        $errores[] = 'Debe seleccionar una habitación.';
    if (empty($fecha_ingreso))      $errores[] = 'La fecha de ingreso es obligatoria.';
    if (empty($fecha_salida))       $errores[] = 'La fecha de salida es obligatoria.';
    if (!empty($fecha_ingreso) && !empty($fecha_salida) && $fecha_salida <= $fecha_ingreso)
        $errores[] = 'La fecha de salida debe ser posterior a la de ingreso.';
    if ($numero_personas <= 0)      $errores[] = 'El número de personas debe ser al menos 1.';

    $estados_validos = ['confirmada', 'cancelada', 'finalizada'];
    if (empty($estado) || !in_array($estado, $estados_validos))
        $errores[] = 'El estado seleccionado no es válido.';

    
    if (empty($errores)) {

        
        $stmt = $conn->prepare("SELECT capacidad, precio FROM habitaciones WHERE id_habitacion = ? AND estado = 'activa' LIMIT 1");
        $stmt->bind_param('i', $habitacion_id);
        $stmt->execute();
        $hab = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$hab) {
            $errores[] = 'La habitación seleccionada no está disponible.';
        } else {
            if ($numero_personas > $hab['capacidad']) {
                $errores[] = "La habitación tiene capacidad máxima de {$hab['capacidad']} personas.";
            }

            
            $stmt = $conn->prepare("
                SELECT COUNT(*) AS cnt
                FROM reservas
                WHERE habitacion_id = ?
                  AND id_reserva   != ?
                  AND estado IN ('finalizada', 'confirmada')
                  AND fecha_ingreso  < ?
                  AND fecha_salida   > ?
            ");
            $stmt->bind_param('iiss', $habitacion_id, $id_reserva, $fecha_salida, $fecha_ingreso);
            $stmt->execute();
            $cnt = $stmt->get_result()->fetch_assoc()['cnt'];
            $stmt->close();

            if ($cnt > 0) {
                $errores[] = 'La habitación no está disponible en las fechas seleccionadas.';
            }

            
            $stmt = $conn->prepare("
                SELECT COUNT(*) AS cnt FROM disponibilidad
                WHERE habitacion_id = ? AND fecha >= ? AND fecha < ?
            ");
            $stmt->bind_param('iss', $habitacion_id, $fecha_ingreso, $fecha_salida);
            $stmt->execute();
            $cnt2 = $stmt->get_result()->fetch_assoc()['cnt'];
            $stmt->close();

            if ($cnt2 > 0) {
                $errores[] = 'La habitación tiene días bloqueados en esas fechas.';
            }
        }
    }

    

        if (empty($errores)) {
            $stmt = $conn->prepare("
                UPDATE reservas
                SET cliente_documento = ?,
                    habitacion_id     = ?,
                    fecha_ingreso     = ?,
                    fecha_salida      = ?,
                    numero_personas   = ?,
                    estado            = ?
                WHERE id_reserva = ?
            ");
           
            $stmt->bind_param('iissisi',
                $cliente_documento,
                $habitacion_id,
                $fecha_ingreso,
                $fecha_salida,
                $numero_personas,
                $estado,
                $id_reserva
            );

            if ($stmt->execute()) {
                $stmt->close();
                header('Location: ver_reservas.php');
                exit;
            } else {
                $errores[] = 'Error al actualizar la reserva. Intenta de nuevo.';
                $stmt->close();
            }
        }
    }



$res_clientes = mysqli_query($conn, "SELECT documento, nombre, apellidos FROM clientes WHERE estado = 'activo' ORDER BY nombre");
$res_hab      = mysqli_query($conn, "SELECT id_habitacion, numero, tipo_habitacion, precio FROM habitaciones WHERE estado = 'activa' ORDER BY numero");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Editar Reserva</div>
        </div>
    </div>

    <div class="card-body">

        <?php if (!empty($errores)): ?>
        <div class="errors-box">
            <div class="errors-title">⚠ Errores</div>
            <?php foreach ($errores as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>


        <form method="POST" enctype="multipart/form-data">

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Cliente</label>
                        <select name="cliente_documento" required>
                            <option value="">Seleccione un cliente…</option>
                            <?php while ($c = mysqli_fetch_assoc($res_clientes)): ?>
                                <option value="<?php echo $c['documento']; ?>"
                                    <?php echo ($reserva['cliente_documento'] == $c['documento']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars("{$c['documento']} — {$c['nombre']} {$c['apellidos']}"); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Habitación</label>
                        <select name="habitacion_id" id="habitacion_id" required onchange="calcularResumen()">
                            <option value="">Seleccione una habitación…</option>
                            <?php while ($h = mysqli_fetch_assoc($res_hab)): ?>
                                <option value="<?php echo $h['id_habitacion']; ?>"
                                        data-precio="<?php echo $h['precio']; ?>"
                                    <?php echo ($reserva['habitacion_id'] == $h['id_habitacion']) ? 'selected' : ''; ?>>
                                    N° <?php echo htmlspecialchars("{$h['numero']} — {$h['tipo_habitacion']} — $" . number_format($h['precio'], 0, ',', '.')); ?>/noche
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso"
                               value="<?php echo htmlspecialchars($reserva['fecha_ingreso']); ?>"
                               onchange="actualizarMinSalida()" required>
                    </div>
                    <div class="field">
                        <label>Fecha de salida</label>
                        <input type="date" name="fecha_salida" id="fecha_salida"
                               value="<?php echo htmlspecialchars($reserva['fecha_salida']); ?>"
                               onchange="calcularResumen()" required>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Número de personas</label>
                        <input type="number" name="numero_personas" min="1" max="20"
                               value="<?php echo htmlspecialchars($reserva['numero_personas']); ?>" required>
                    </div>
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado" required>
                            <?php foreach (['pendiente','confirmada','cancelada','finalizada'] as $e): ?>
                                <option value="<?php echo $e; ?>"
                                    <?php echo ($reserva['estado'] === $e) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($e); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
                <span class="footer-note"><span>*</span> Campos obligatorios</span>
                <div class="footer-actions">
                    <a href="ver_reservas.php" class="btn btn-back">Volver</a>
                    <button type="submit" class="btn btn-submit"><span>Guardar cambios</span></button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
function actualizarMinSalida() {
    const ingreso = document.getElementById('fecha_ingreso').value;
    if (!ingreso) return;
    const sig = new Date(ingreso);
    sig.setDate(sig.getDate() + 1);
    const min = sig.toISOString().split('T')[0];
    const salida = document.getElementById('fecha_salida');
    salida.min = min;
    if (salida.value && salida.value <= ingreso) salida.value = min;
    calcularResumen();
}

</script>
</body>
</html>
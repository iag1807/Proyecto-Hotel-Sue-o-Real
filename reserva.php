<?php
session_start();
require_once '../conexion.php';

$sql_clientes = "SELECT documento, nombre, apellidos FROM clientes WHERE estado = 'activo' ORDER BY nombre";
$res_clientes = mysqli_query($conn, $sql_clientes);


$sql_hab = "SELECT id_habitacion, numero, tipo_habitacion, precio, precio_persona_adicional FROM habitaciones WHERE estado = 'activa' ORDER BY numero";
$res_hab = mysqli_query($conn, $sql_hab);


$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Reserva</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Registrar Reserva</div>
            <div class="header-sub">Nueva Reserva</div>
        </div>
    </div>

    <div class="card-body">

        <?php if (isset($_SESSION['errores'])): ?>
        <div class="errors-box">
            <div class="errors-title">⚠ Errores</div>
            <?php foreach ($_SESSION['errores'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['errores']); ?>
        </div>
        <?php endif; ?>

        <form action="validar_reserva.php" method="POST" enctype="multipart/form-data">

            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Cliente</label>
                        <select name="cliente_documento" required>
                            <option value="">Seleccione un cliente…</option>
                            <?php while ($c = mysqli_fetch_assoc($res_clientes)): ?>
                                <option value="<?php echo $c['documento']; ?>"
                                    <?php echo (($old['cliente_documento'] ?? '') == $c['documento']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars("{$c['documento']} — {$c['nombre']} {$c['apellidos']}"); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Habitación</label>
                        <select name="habitacion_id" id="habitacion_id" required onchange="actualizarPrecio(this)">
                            <option value="">Seleccione una habitación…</option>
                            <?php while ($h = mysqli_fetch_assoc($res_hab)): ?>
                                <option value="<?php echo $h['id_habitacion']; ?>"
                                        data-precio="<?php echo $h['precio']; ?>"
                                        data-precio-extra="<?php echo $h['precio_persona_adicional'] ?? 0; ?>"
                                    <?php echo (($old['habitacion_id'] ?? '') == $h['id_habitacion']) ? 'selected' : ''; ?>>
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
                        <div class="field-icon">
                            <input type="date" name="fecha_ingreso" id="fecha_ingreso"
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo htmlspecialchars($old['fecha_ingreso'] ?? ''); ?>"
                                   onchange="actualizarMinSalida()" required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Fecha de salida</label>
                        <div class="field-icon">
                            <input type="date" name="fecha_salida" id="fecha_salida"
                                   value="<?php echo htmlspecialchars($old['fecha_salida'] ?? ''); ?>"
                                   onchange="calcularResumen()" required>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Número de personas</label>
                        <input type="number" name="numero_personas" id="numero_personas" min="1" max="20" onchange="calcularResumen()"
                               value="<?php echo htmlspecialchars($old['numero_personas'] ?? ''); ?>" required>
                    </div>
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado" required>
                            <option value="">Seleccione…</option>
                            <?php foreach (['confirmada','cancelada','finalizada'] as $e): ?>
                                <option value="<?php echo $e; ?>"
                                    <?php echo (($old['estado'] ?? '') === $e) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($e); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

           
            <div class="form-section">
                <div class="field">
                    <label>Comprobante de pago (anticipo 50%)</label>
                    <input type="file" name="comprobante_pago" accept="image/jpeg,image/png,image/webp,application/pdf" required>
                </div>
            </div>

            
            <div id="resumen" style="display:none; background:#333; border:1px solid #d4b87a; border-radius:4px; padding:1rem 1.25rem; margin-top:0.5rem; font-size:0.88rem; color:#555;">
                <strong style="display:block;margin-bottom:0.5rem;color:#333">Resumen de la reserva</strong>
                <div style="display:flex;justify-content:space-between"><span>Noches:</span><span id="r-noches">—</span></div>
                <div style="display:flex;justify-content:space-between"><span>Precio por noche:</span><span id="r-precio">—</span></div>
                <div style="display:flex;justify-content:space-between;font-weight:600;margin-top:0.5rem;border-top:1px solid #d4b87a;padding-top:0.5rem"><span>Total estadía:</span><span id="r-total">—</span></div>
                <div style="display:flex;justify-content:space-between;color:#b8860b;font-weight:700;font-size:0.95rem"><span>Anticipo a pagar (50%):</span><span id="r-anticipo">—</span></div>
            </div>

            <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
                <span class="footer-note"><span>*</span> Campos obligatorios</span>
                <div class="footer-actions">
                    <a href="ver_reservas.php" class="btn btn-back">Volver</a>
                    <button type="submit" class="btn btn-submit"><span>Registrar reserva</span></button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
function actualizarMinSalida() {
    const ingreso = document.getElementById('fecha_ingreso').value;
    if (!ingreso) return;
    const siguiente = new Date(ingreso);
    siguiente.setDate(siguiente.getDate() + 1);
    const min = siguiente.toISOString().split('T')[0];
    const salida = document.getElementById('fecha_salida');
    salida.min = min;
    if (salida.value && salida.value <= ingreso) {
        salida.value = min;
    }
    calcularResumen();
}

function actualizarPrecio() {
    calcularResumen();
}

function calcularResumen() {
    const ingreso  = document.getElementById('fecha_ingreso').value;
    const salida   = document.getElementById('fecha_salida').value;
    const select   = document.getElementById('habitacion_id');
    const personas = parseInt(document.getElementById('numero_personas').value) || 1;
    const opt      = select.options[select.selectedIndex];

    const precioBase  = parseFloat(opt?.dataset?.precio      || 0);
    const precioExtra = parseFloat(opt?.dataset?.precioExtra || 0);

    if (!ingreso || !salida || !precioBase || salida <= ingreso) {
        document.getElementById('resumen').style.display = 'none';
        return;
    }

    // Calcular precio por noche según personas
    const personasExtra = Math.max(0, personas - 1);
    const precioNoche   = precioBase + (precioExtra * personasExtra);

    const noches  = (new Date(salida) - new Date(ingreso)) / 86400000;
    const total   = noches * precioNoche;
    const anticipo = total / 2;

    document.getElementById('r-noches').textContent   = noches;
    document.getElementById('r-precio').textContent   = '$ ' + precioNoche.toLocaleString('es-CO') + ' / noche';
    document.getElementById('r-total').textContent    = '$ ' + total.toLocaleString('es-CO');
    document.getElementById('r-anticipo').textContent = '$ ' + anticipo.toLocaleString('es-CO');
    document.getElementById('resumen').style.display  = 'block';
}
</script>
</body>
</html>
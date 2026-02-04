<?php
require_once '../conexion.php';

$sql = "SELECT r.*, c.documento, c.nombre, c.apellidos, h.numero 
        FROM reservas AS r
        JOIN clientes AS c ON r.cliente_documento = c.documento
        JOIN habitaciones AS h ON r.habitacion_numero = h.numero";

$resultado= mysqli_query($conn, $sql);
$total= mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver reservas</title>
    <link rel="stylesheet" href="../estilos/styles3.css">
</head>
<body>
    <a href="registrar_reserva.php"><button>Registrar una nueva reserva</button></a>
    <div class="container">
    <h1>Reservas registradas 📅</h1>
    <table>
        <thead>
            <tr>
                <th>Documento cliente</th>
                <th>Nombre cliente</th>
                <th>Fecha de ingreso</th>
                <th>Fecha de salida</th>
                <th>Numero de personas</th>
                <th>Habitacion</th>
                <th>Estado</th>
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
                <td><?php echo htmlspecialchars($fila['habitacion_numero']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a href="editar_reservas.php?id_reserva=<?php echo $fila['id_reserva']; ?>">Editar ✏️</a>
                    <a href="desactivar_reservas.php?id_reserva=<?php echo $fila['id_reserva']; ?>">Desactivar ❌</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <a href="../admin.php"><button>Volver</button></a>
</body>
</html>
<?php
require_once '../conexion.php';

$sql= "SELECT * FROM habitaciones";
$resultado= mysqli_query($conn, $sql);
$total= mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver clientes</title>
    <link rel="stylesheet" href="../estilos/styles3.css">
</head>
<body>
    <div class="container">
    <h1>Habitaciones registradas 🏠</h1>
    <table>
        <thead>
            <tr>
                <th>Numero</th>
                <th>Tipo de habitacion</th>
                <th>Precio</th>
                <th>Descripcion</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['numero']); ?></td>
                <td><?php echo htmlspecialchars($fila['tipo_habitacion']); ?></td>
                <td><?php echo htmlspecialchars($fila['precio']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a href="editar_habitaciones.php?numero=<?php echo $fila['numero']; ?>">Editar ✏️</a>
                    <a href="desactivar_habitaciones.php?numero=<?php echo $fila['numero']; ?>">Desactivar ❌</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <a href="../admin.php"><button>Volver</button></a>
</body>
</html>
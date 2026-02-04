<?php
require_once '../conexion.php';

$sql= "SELECT * FROM clientes";
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
    <a href="registrar_cliente.php"><button>Registrar un nuevo cliente</button></a>
    <div class="container">
    <h1>Clientes registrados 👤</h1>
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Tipo de documento</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Correo</th>
                <th>Direccion</th>
                <th>Celular</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fila= mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['documento']); ?></td>
                <td><?php echo htmlspecialchars($fila['tipo_documento']); ?></td>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                <td><?php echo htmlspecialchars($fila['direccion']); ?></td>
                <td><?php echo htmlspecialchars($fila['celular']); ?></td>
                <td><?php echo htmlspecialchars($fila['rol']); ?></td>
                <td><?php echo htmlspecialchars($fila['estado']); ?></td>
                <td>
                    <a href="editar_clientes.php?documento=<?php echo $fila['documento']; ?>">Editar ✏️</a>
                    <a href="desactivar_clientes.php?documento=<?php echo $fila['documento']; ?>">Desactivar ❌</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
         <?php if(isset($_SESSION['success'])) : ?>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </table>
    </div>
    <a href="../admin.php"><button>Volver</button></a>
</body>
</html>
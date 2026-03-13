<?php
require_once '../conexion.php';

if($_SERVER['REQUEST_METHOD'] === "POST"){

    if(isset($_POST['id_habitacion'], $_POST['tipo_habitacion'], $_POST['capacidad'], $_POST['precio'], $_POST['descripcion'], $_POST['estado'])){

        $id_habitacion = $_POST['id_habitacion'];
        $tipo_habitacion = $_POST['tipo_habitacion'];
        $capacidad = $_POST['capacidad'];
        $precio = $_POST['precio'];
        $descripcion = $_POST['descripcion'];
        $estado = $_POST['estado'];

        $sql = "UPDATE habitaciones 
                SET tipo_habitacion=?, capacidad=?, precio=?, descripcion=?, estado=? 
                WHERE id_habitacion=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $tipo_habitacion, $capacidad, $precio, $descripcion, $estado, $id_habitacion);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ver_habitaciones.php");
        exit();
    }
}

if(isset($_GET['id_habitacion']) && is_numeric($_GET['id_habitacion'])){

    $id_habitacion = $_GET['id_habitacion'];

    $sql = "SELECT * FROM habitaciones WHERE id_habitacion = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id_habitacion);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Habitación</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Editar Habitación</div>
            <div class="header-sub">Modificar habitación</div>
        </div>
    </div>

    <div class="card-body">

        <form action="editar_habitaciones.php" method="POST">
            <input type="hidden" name="id_habitacion" value="<?= $fila['id_habitacion'] ?>">
            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Numero</label>
                        <div class="doc-badge">
                            <span><?= htmlspecialchars($fila['numero']) ?></span>
                            <small>No editable</small>
                        </div>
                    </div>
                    <div class="field">
                        <label>Tipo de habitación</label>
                        <select name="tipo_habitacion" id="tipo_habitacion">
                            <option value="">Seleccione…</option>
                            <option value="sencilla" <?php echo ($fila['tipo_habitacion'] == 'sencilla') ? 'selected' : ''; ?>>Sencilla</option>
                            <option value="bañera" <?php echo ($fila['tipo_habitacion'] == 'bañera') ? 'selected' : ''; ?>>Con Bañera</option>
                            <option value="jacuzzi" <?php echo ($fila['tipo_habitacion'] == 'jacuzzi') ? 'selected' : ''; ?>>Con Jacuzzi</option>
                            <option value="doble" <?php echo ($fila['tipo_habitacion'] == 'doble') ? 'selected' : ''; ?>>Doble</option>
                            <option value="triple" <?php echo ($fila['tipo_habitacion'] == 'triple') ? 'selected' : ''; ?>>Triple</option>
                            <option value="multiple" <?php echo ($fila['tipo_habitacion'] == 'multiple') ? 'selected' : ''; ?>>Múltiple</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Capacidad</label>
                        <input type="text" name="capacidad" value="<?= htmlspecialchars($fila['capacidad']) ?>">
                    </div>
                    <div class="field">
                        <label>Precio</label>
                        <input type="text" name="precio" value="<?= htmlspecialchars($fila['precio']) ?>">
                    </div>
                </div>
                <div class="form-grid col-2" style="margin-top:1rem">
                    <div class="field">
                        <label>Descripción</label>
                        <div class="field-icon">
                            <input type="text" name="descripcion" value="<?= htmlspecialchars($fila['descripcion']) ?>">
                        </div>
                    </div>    
                
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado" id="estado">
                            <option value="">Seleccione…</option>
                            <option value="activa" <?php echo ($fila['estado'] == 'activa') ? 'selected' : ''; ?>>Activa</option>
                            <option value="mantenimiento" <?php echo ($fila['estado'] == 'mantenimiento') ? 'selected' : ''; ?>>Mantenimiento</option>
                        </select>
                    </div>
                </div>
            </div>
            

        <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
            <span class="footer-note"><span>*</span> Campos obligatorios</span>
            <div class="footer-actions">
                <a href="ver_habitaciones.php" class="btn btn-back">Volver</a>
                <button type="submit" class="btn btn-submit">Actualizar habitación</button>
            </div>
        </div>

        </form>
    </div>

</div>

</body>
</html>
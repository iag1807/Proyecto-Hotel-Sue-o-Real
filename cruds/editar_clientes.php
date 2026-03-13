<?php
require_once '../conexion.php';

if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(isset($_POST['documento'], $_POST['tipo_documento'], $_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['genero'], $_POST['celular'], $_POST['rol'], $_POST['estado'])){
        $documento= $_POST['documento'];
        $tipo_documento= $_POST['tipo_documento'];
        $nombre= $_POST['nombre'];
        $apellidos= $_POST['apellidos'];
        $correo= $_POST['correo'];
        $genero= $_POST['genero'];
        $celular= $_POST['celular'];
        $rol= $_POST['rol'];
        $estado= $_POST['estado'];

        $sql= "UPDATE clientes SET tipo_documento= ?, nombre= ?, apellidos= ?, correo= ?, genero= ?, celular= ?, rol= ?, estado= ? WHERE documento= ?";
        $stmt=mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $tipo_documento, $nombre, $apellidos, $correo, $genero, $celular, $rol, $estado, $documento);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ver_clientes.php");
        exit();
    }
}

if(isset($_GET['documento']) && is_numeric($_GET['documento'])){
    $documento = $_GET['documento'];
    $sql="SELECT * FROM clientes WHERE documento= ?";
    $stmt= mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $documento);
    mysqli_stmt_execute($stmt);
    $resultado= mysqli_stmt_get_result($stmt);
    $fila= mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Huésped — Hotel Sueño Real</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Editar Huésped</div>
            <div class="header-sub">Modificar registro</div>
        </div>
    </div>

    <div class="card-body">
        <form action="editar_clientes.php" method="POST">
            <input type="hidden" name="documento" value="<?= $fila['documento'] ?>">

            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Documento</label>
                        <div class="doc-badge">
                            <span><?= htmlspecialchars($fila['documento']) ?></span>
                            <small>No editable</small>
                        </div>
                    </div>
                    <div class="field">
                        <label>Tipo de documento</label>
                        <select name="tipo_documento" id="tipo_documento">
                            <option value="">Seleccione…</option>
                            <option value="C.C" <?php echo ($fila['tipo_documento'] == 'C.C') ? 'selected' : ''; ?>>C.C — Cédula de ciudadanía</option>
                            <option value="C.T" <?php echo ($fila['tipo_documento'] == 'C.T') ? 'selected' : ''; ?>>C.E — Cédula de extranjería</option>
                            <option value="T.I" <?php echo ($fila['tipo_documento'] == 'T.I') ? 'selected' : ''; ?>>T.I — Tarjeta de identidad</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($fila['nombre']) ?>">
                    </div>
                    <div class="field">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="<?= htmlspecialchars($fila['apellidos']) ?>">
                    </div>
                </div>
                <div class="form-grid" style="margin-top:1rem">
                    <div class="field">
                        <label>Género</label>
                        <select name="genero" id="genero">
                            <option value="">Seleccione…</option>
                            <option value="masculino" <?php echo ($fila['genero'] == 'masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="femenino"  <?php echo ($fila['genero'] == 'femenino')  ? 'selected' : ''; ?>>Femenino</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Correo electrónico</label>
                        <div class="field-icon">
                            <input type="text" name="correo" value="<?= htmlspecialchars($fila['correo']) ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label>Celular</label>
                        <div class="field-icon">
                            <input type="text" name="celular" value="<?= htmlspecialchars($fila['celular']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section" style="margin-bottom:0">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Rol</label>
                        <select name="rol" id="rol">
                            <option value="">Seleccione…</option>
                            <option value="admin"          <?php echo ($fila['rol'] == 'admin')          ? 'selected' : ''; ?>>Administrador</option>
                            <option value="cliente"        <?php echo ($fila['rol'] == 'cliente')        ? 'selected' : ''; ?>>Cliente</option>
                            <option value="recepcionista"  <?php echo ($fila['rol'] == 'recepcionista')  ? 'selected' : ''; ?>>Recepcionista</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado" id="estado">
                            <option value="">Seleccione…</option>
                            <option value="activo"   <?php echo ($fila['estado'] == 'activo')   ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo ($fila['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

        <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
            <span class="footer-note"><span>*</span> Campos obligatorios</span>
            <div class="footer-actions">
                <a href="ver_clientes.php" class="btn btn-back">Volver</a>
                <button type="submit" class="btn btn-submit">Actualizar huésped</button>
            </div>
        </div>

        </form>
    </div>

</div>

</body>
</html>
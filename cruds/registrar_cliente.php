<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar cliente</title>
</head>
<body>
    <h1>Registrar cliente</h1>
    <form action="validar_cliente.php" method="POST">
        <label for="documento">Documento</label><br>
        <input type="text" name="documento"><br>
        <label for="tipo_documento">Tipo de documento</label><br>
        <select name="tipo_documento" id="tipo_documento">
            <option value="">Seleccione un tipo de documento</option>
            <option value="C.C">Cedula de ciudadania</option>
            <option value="C.T">Cedula de extranjeria</option>
            <option value="T.I">Tarjeta de identidad</option>
        </select><br>
        <label for="nombre">Nombre</label><br>
        <input type="text" name="nombre"><br>
        <label for="apellidos">Apellidos</label><br>
        <input type="text" name="apellidos"><br>
        <label for="correo">Correo</label><br>
        <input type="text" name="correo"><br>
        <label for="clave">Contraseña</label><br>
        <input type="password" name="clave"><br>
        <label for="direccion">Direccion</label><br>
        <input type="text" name="direccion"><br>
        <label for="celular">Celular</label><br>
        <input type="text" name="celular"><br>
        <label for="rol">Rol</label><br>
        <select name="rol">
            <option value="">Seleccione un rol</option>
            <option value="admin">Administrador</option>
            <option value="cliente">Cliente</option>
            <option value="recepcionista">Recepcionista</option>
        </select><br>
        <label for="estado">Estado</label><br>
        <select name="estado">
            <option value="">Seleccione un estado</option>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select><br><br>
        <input type="submit" value="Registrar"><br><br>

        <?php if(isset($_SESSION['errores'])) : ?>
            <?php 
            foreach($_SESSION['errores'] as $error){
            echo "<p>".htmlspecialchars($error). "</p>";
            }
            unset($_SESSION['errores']);
            ?>
        <?php endif; ?>
    </form>
    <a href="../admin.php"><button>Volver</button></a>
</body>
</html>
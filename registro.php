<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="shortcut icon" href="imagenes/LogoHotel.png">
    <link rel="stylesheet" href="estilos/style-registros.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
    <h1>Registrate</h1>
    <p>y disfruta de los servicios de nuestro hotel</p>
    <form action="validaciones/validar_registro.php" method="POST">
        <div class="form-grid">
            <label for="documento"><span class="label-text">Documento</span>
                <input type="text" name="documento" id="documento">
            </label>

            <label for="tipo_documento"><span class="label-text">Tipo de documento</span>
                <select name="tipo_documento" id="tipo_documento" class="input-select">
                    <option value="">Seleccione un tipo de documento</option>
                    <option value="C.C">Cedula de ciudadania</option>
                    <option value="C.T">Cedula de extranjeria</option>
                    <option value="T.I">Tarjeta de identidad</option>
                </select>
            </label>

            <label for="nombre"><span class="label-text">Nombre</span>
                <input type="text" name="nombre" id="nombre">
            </label>

            <label for="apellidos"><span class="label-text">Apellidos</span>
                <input type="text" name="apellidos" id="apellidos">
            </label>

            <label for="correo"><span class="label-text">Correo</span>
                <input type="text" name="correo" id="correo">
            </label>

            <label for="clave"><span class="label-text">Contraseña</span>
                <input type="password" name="clave" id="clave">
            </label>

            <label for="direccion"><span class="label-text">Direccion</span>
                <input type="text" name="direccion" id="direccion">
            </label>

            <label for="celular"><span class="label-text">Celular</span>
                <input type="text" name="celular" id="celular">
            </label>
        </div>

        <button type="submit" class="submit-btn">Registrarse</button>

        <p>¿Ya tienes cuenta? <a href="inicio_sesion.php"><br>Iniciar Sesión</a></p>

        <?php if(isset($_SESSION['errores'])) : ?>
            <?php 
            foreach($_SESSION['errores'] as $error){
            echo "<p>".htmlspecialchars($error). "</p>";
            }
            unset($_SESSION['errores']);
            ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])) : ?>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </form>
    </div>
    <a href="index.html"><button class="btn">Volver</button></a>
</body>
</html>
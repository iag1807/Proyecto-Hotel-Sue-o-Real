<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link rel="shortcut icon" href="imagenes/LogoHotel.png">
    <link rel="stylesheet" href="estilos/style-registros.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond&display=swap" rel="stylesheet"/>
</head>

<body>
    <div class="container2">
        <h1>Iniciar sesion</h1>
        <form action="validaciones/validar_iniciodesesion.php" method="POST">
        <div class="form-grid2">
            <label for="correo"><span class="label-text">Correo</span>
                <input type="text" name="correo" id="correo">
            </label>

            <label for="clave"><span class="label-text">Contraseña</span>
                <input type="password" name="clave" id="clave">
            </label>
        </div>

            <button type="submit" class="submit-btn2">Iniciar sesión</button>

            <p>¿Olvidaste tu contraseña?<br>↓ <br><a href="recuperar_contraseña.php">Recuperar</a></p>

            <?php if (isset($_SESSION['errores'])) : ?>
                <?php
                foreach ($_SESSION['errores'] as $error) {
                    echo "<p>" . htmlspecialchars($error) . "</p>";
                }
                unset($_SESSION['errores']);
                ?>
            <?php endif; ?>
        </form>
    </div>
    <a href="registro.php"><button class="btn">←</button></a>
</body>

</html>
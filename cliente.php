<?php
session_start();

if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente'){
    header("location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Sueño Real</title>
    <link rel="stylesheet" href="estilos/style-clientes.css">
     <link rel="shortcut icon" href="imagenes/LogoHotel.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marcellus&display=swap" rel="stylesheet">
</head>
<body>
    <?php if(isset($_SESSION['success'])) : ?>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <header>
        <img class="logo" src="imagenes/LogoHotel.png." alt="">
        <div class="section-header">
            <h1 class="section-title"><span> ELIGE </span> UNA HABITACION</h1>
        </div>
        <a href="iniciodesesion/cerrarsesion.php"><button class="btn">Cerrar sesion</button></a>
    </header>

    <section class="habitaciones-section">
        

        <div class="habitaciones-grid">
            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/sencilla.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Sencilla</h3>
                    <p class="habitacion-detalles">Cama semidoble, baño privado, televisor</p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/sencilla.php" class="btn-reservar"> Ver detalles</a>
                </div>
            </div>

            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/bañera.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Con bañera</h3>
                    <p class="habitacion-detalles">Cama semidoble, baño privado, bañera, televisor </p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/bañera.php" class="btn-reservar"> Ver detalles</a>
                </div>
            </div>

            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/jacuzzi.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Con jacuzzi</h3>
                    <p class="habitacion-detalles">Cama semidoble, baño privado, jacuzzi, televisor</p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/jacuzzi.php" class="btn-reservar">Ver detalles</a>
                </div>
            </div>

            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/doble.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Doble</h3>
                    <p class="habitacion-detalles">Dos camas semidobles, baño privado, televisor</p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/doble.php" class="btn-reservar">Ver detalles</a>
                </div>
            </div>

            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/triple.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Triple</h3>
                    <p class="habitacion-detalles">Una cama semidoble, un camarote, baño privado, televisor</p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/triple.php" class="btn-reservar">Ver detalles</a>
                </div>
            </div>

            <div class="habitacion-card">
                <img class="habitacion-imagen" src="imagenes/multiple.jpeg" alt="">
                <div class="habitacion-content">
                    <h3 class="habitacion-nombre">Multiple</h3>
                    <p class="habitacion-detalles">Dos camarotes, una cama de un metro, baño privado, televisor</p>
                    <p class="habitacion-detalles">Internet gratuito</p>
                    <a href="habitaciones/multiple.php" class="btn-reservar">Ver detalles</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
    <img class="logo" src="imagenes/LogoHotel.png" alt="">
    </footer>
</body>
</html>
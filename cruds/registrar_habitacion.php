<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Habitacion</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Registrar Habitacion</div>
            <div class="header-sub">Nueva habitación</div>
        </div>
    </div>

   
    <div class="card-body">

        <?php if(isset($_SESSION['errores'])) : ?>
        <div class="errors-box">
            <div class="errors-title">⚠ Errores</div>
            <?php
            foreach($_SESSION['errores'] as $error){
                echo "<p>" . htmlspecialchars($error) . "</p>";
            }
            unset($_SESSION['errores']);
            ?>
        </div>
        <?php endif; ?>

        <form action="validar_habitacion.php" method="POST">
            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Numero</label>
                        <div class="field-icon">
                            <input type="text" name="numero">
                        </div>
                    </div>
                    <div class="field">
                        <label>Tipo de habitación</label>
                        <select name="tipo_habitacion" id="tipo_habitacion">
                            <option value="">Seleccione…</option>
                            <option value="sencilla">Sencilla</option>
                            <option value="bañera">Con Bañera</option>
                            <option value="jacuzzi">Con Jacuzzi</option>
                            <option value="doble">Doble</option>
                            <option value="triple">Triple</option>
                            <option value="multiple">Múltiple</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Capacidad</label>
                        <input type="text" name="capacidad">
                    </div>
                    <div class="field">
                        <label>Precio</label>
                        <input type="text" name="precio">
                    </div>
                </div>
                <div class="form-grid col-2" style="margin-top:1rem">
                    <div class="field">
                        <label>Descripción</label>
                        <div class="field-icon">
                            <input type="text" name="descripcion">
                        </div>
                    </div>    
                
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado" id="estado">
                            <option value="">Seleccione…</option>
                            <option value="activa">Activa</option>
                            <option value="mantenimiento">En Mantenimiento</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
                <span class="footer-note"><span>*</span> Campos obligatorios</span>
                <div class="footer-actions">
                    <a href="ver_habitaciones.php" class="btn btn-back"> Volver</a>
                    <button type="submit" class="btn btn-submit"><span>Registrar habitación</span></button>
                </div>
            </div>

        </form>
    </div>
</div>
</body>
</html>
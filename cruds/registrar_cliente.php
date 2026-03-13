<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../imagenes/LogoHotel.png">
    <link rel="stylesheet" href="../estilos/style-cruds-registros.css">
</head>
<body>

<div class="card">

    <div class="card-header">
        <div>
            <div class="header-title">Registrar Cliente</div>
            <div class="header-sub">Nuevo registro</div>
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

        <form action="validar_cliente.php" method="POST">
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Tipo de documento</label>
                        <select name="tipo_documento" id="tipo_documento">
                            <option value="">Seleccione…</option>
                            <option value="C.C">C.C — Cédula de ciudadanía</option>
                            <option value="C.T">C.E — Cédula de extranjería</option>
                            <option value="T.I">T.I — Tarjeta de identidad</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Número de documento</label>
                        <div class="field-icon">
                            <input type="text" name="documento">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Nombre</label>
                        <input type="text" name="nombre">
                    </div>
                    <div class="field">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos">
                    </div>
                </div>
                <div class="form-grid col-2" style="margin-top:1rem">
                    <div class="field">
                        <label>Correo electrónico</label>
                        <div class="field-icon">
                            <input type="text" name="correo">
                        </div>
                    </div>    
                
                    <div class="field">
                        <label>Contraseña</label>
                        <div class="field-icon">
                            <input type="password" name="clave">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="form-section">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Género</label>
                        <select name="genero" id="genero">
                            <option value="">Seleccione…</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Celular</label>
                        <div class="field-icon">
                            <input type="text" name="celular">
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="form-section" style="margin-bottom:0">
                <div class="form-grid col-2">
                    <div class="field">
                        <label>Rol</label>
                        <select name="rol">
                            <option value="">Seleccione…</option>
                            <option value="admin">Administrador</option>
                            <option value="cliente">Cliente</option>
                            <option value="recepcionista">Recepcionista</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Seleccione…</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="card-footer" style="margin: 1.8rem -2rem -1.8rem; padding-left:2rem; padding-right:2rem;">
                <span class="footer-note"><span>*</span> Campos obligatorios</span>
                <div class="footer-actions">
                    <a href="ver_clientes.php" class="btn btn-back"> Volver</a>
                    <button type="submit" class="btn btn-submit"><span>Registrar cliente</span></button>
                </div>
            </div>

        </form>
    </div>
</div>
</body>
</html>
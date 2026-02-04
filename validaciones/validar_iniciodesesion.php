<?php
require_once '../iniciodesesion/sesion.php';
iniciarSesion();

require_once '../conexion.php';


if($_SERVER['REQUEST_METHOD'] !=='POST'){
    header("Location: ..inicio_sesion.php");
}

$correo= $_POST['correo'] ?? '';
$clave= $_POST['clave'] ?? '';
$errores= [];

if(empty($correo) || empty($clave)){
    $errores[]="Debe ingresar correo y contraseña"; 
}

if(empty($errores)){
    $sql= "SELECT documento, correo, clave, rol FROM clientes WHERE correo= ?";
    $stmt= mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado= mysqli_stmt_get_result($stmt);
    $datos= mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if($datos && password_verify($clave, $datos['clave'])){
        $_SESSION['documento']= $datos['documento'];
        $_SESSION['correo']= $datos['correo'];
        $_SESSION['rol']= $datos['rol'];

        switch($datos['rol']){
            case 'admin':
                header("Location: ../admin.php");
                break;
            case 'cliente':
                header("Location: ../cliente.php");
                break;
            case 'recepcionista':
                header("Location: ../recepcionista.php");
                break;
            default:
                header("Location: ../inicio_sesion.php");
                break;
        }
    }else{
        $errores[]="Correo o contraseña incorrectos";
    }
}

if(!empty($errores)){
    $_SESSION['errores'] = $errores;
    header("Location: ../inicio_sesion.php");
    exit();
}
?>
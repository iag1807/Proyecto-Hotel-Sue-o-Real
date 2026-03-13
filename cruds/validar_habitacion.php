<?php
session_start();

$numero= $_POST['numero'];
$tipo_habitacion= $_POST['tipo_habitacion'];
$capacidad= $_POST['capacidad'];
$precio= $_POST['precio'];
$descripcion= $_POST['descripcion'];
$estado= $_POST['estado'];
$errores= [];

if(empty($_POST['numero']) || empty($_POST['tipo_habitacion']) || empty($_POST['capacidad']) || empty($_POST['precio']) || empty($_POST['descripcion']) || empty($_POST['estado'])){
    $errores[]="El formulario esta incompleto";

}elseif(!ctype_digit($numero)) {
    $errores[] = "El número de la habitación solo debe contener números.";

}elseif(!empty($numero)) {
    require_once '../conexion.php';
    $consulta = "SELECT 1 FROM habitaciones WHERE numero = ?";
    $stmt= mysqli_prepare($conn, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $numero);
    mysqli_stmt_execute($stmt);
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $errores[]=  "El número de la habitación ya está registrado";
    }

}elseif(!in_array($tipo_habitacion, ['sencilla','bañera','jacuzzi', 'doble', 'triple', 'multiple'])){
    $errores[]= "Tipo de habitación invalido";

}elseif(!is_numeric($capacidad)){
    $errores[]= "Capacidad invalida";

}elseif(!is_numeric($precio)){
    $errores[]= "Precio invalido";

}elseif(!is_string($descripcion)){
    $errores[]= "La descripción debe ser una cadena de texto";

}elseif(!in_array($estado, ['activa','mantenimiento'])){
    $errores[]= "Estado invalido";
}

if(!empty($errores)){
    $_SESSION['errores'] = $errores;
    header("location: registrar_habitacion.php");
    exit();
}

require_once '../conexion.php';

$sql= "INSERT INTO habitaciones(numero, tipo_habitacion, capacidad, precio, descripcion, estado) VALUES (?,?,?,?,?,?)";
$stmt= mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssss", $numero, $tipo_habitacion, $capacidad, $precio, $descripcion, $estado);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success']= "Habitación registrada correctamente";
header("location: ver_habitaciones.php");
exit();
?>
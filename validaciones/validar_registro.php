<?php
session_start();

if($_SERVER['REQUEST_METHOD'] !=='POST'){
    header("location: ../registro.php");
}

$documento= $_POST['documento'];
$tipo_documento= $_POST['tipo_documento'];
$nombre= $_POST['nombre'];
$apellidos= $_POST['apellidos'];
$correo= $_POST['correo'];
$clave= $_POST['clave'];
$genero= $_POST['genero'];
$celular= $_POST['celular'];
$errores= [];

if(empty($_POST['documento']) || empty($_POST['tipo_documento']) || empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['correo']) || empty($_POST['clave']) || empty($_POST['genero']) || empty($_POST['celular'])){
    $errores[]="El formulario esta incompleto";

}elseif(!ctype_digit($documento)) {
    $errores[] = "El documento solo debe contener números.";

}elseif(!empty($documento)) {
    require_once '../conexion.php';
    $consulta = "SELECT 1 FROM clientes WHERE documento = ?";
    $stmt= mysqli_prepare($conn, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $documento);
    mysqli_stmt_execute($stmt);
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $errores[]=  "El documento ya está registrado";
    }

}elseif(!in_array($tipo_documento, ['C.C','C.T','T.I'])){
    $errores[]= "Tipo de documento invalido";

}elseif(!is_string($nombre)){
    $errores[]= "Nombre invalido";

}elseif(!is_string($apellidos)){
    $errores[]= "Apellidos invalido";

}elseif(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
    $errores[]="Correo invalido";

}elseif(strlen($clave) < 8){
    $errores[]= "La clave debe tener como minimo 8 caracteres";

}elseif(!in_array($genero, ['Masculino','Femenino'])){
    $errores[]= "Genero invalido";

}elseif(!ctype_digit($celular)){
    $errores[]= "El celular solo debe contener numeros";
}

if(!empty($errores)){
    $_SESSION['errores'] = $errores;
    header("location: ../registro.php");
    exit();
}

require_once '../conexion.php';

$clave= password_hash($clave, PASSWORD_DEFAULT);

$sql= "INSERT INTO clientes(documento, tipo_documento, nombre, apellidos, correo, clave, genero, celular) VALUES (?,?,?,?,?,?,?,?)";
$stmt= mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssssss", $documento, $tipo_documento, $nombre, $apellidos, $correo, $clave, $genero, $celular);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success']= "Usuario registrado correctamente";
header("location: ../registro.php");
exit();

?>
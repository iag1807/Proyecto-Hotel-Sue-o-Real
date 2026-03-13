<?php
session_start();

$documento= $_POST['documento'];
$tipo_documento= $_POST['tipo_documento'];
$nombre= $_POST['nombre'];
$apellidos= $_POST['apellidos'];
$correo= $_POST['correo'];
$clave= $_POST['clave'];
$celular= $_POST['celular'];
$genero= $_POST['genero'];
$rol= $_POST['rol']; 
$estado= $_POST['estado'];
$errores= [];

if(empty($_POST['documento']) || empty($_POST['tipo_documento']) || empty($_POST['nombre']) || empty($_POST['apellidos']) || empty($_POST['correo']) || empty($_POST['clave']) || empty($_POST['celular']) || empty($_POST['genero']) || empty($_POST['rol']) || empty($_POST['estado'])){
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

}elseif(!ctype_digit($celular)){
    $errores[]= "El celular solo debe contener numeros";

}elseif(!in_array($genero, ['masculino','femenino'])){
    $errores[]= "Genero invalido";

}elseif(!in_array($rol, ['admin','cliente','recepcionista'])){
    $errores[]= "Rol invalido";

}elseif(!in_array($estado, ['activo','inactivo'])){
    $errores[]= "Estado invalido";
}

if(!empty($errores)){
    $_SESSION['errores'] = $errores;
    header("location: registrar_cliente.php");
    exit();
}

require_once '../conexion.php';

$clave= password_hash($clave, PASSWORD_DEFAULT);

$sql= "INSERT INTO clientes(documento, tipo_documento, nombre, apellidos, correo, clave, celular, genero, rol, estado) VALUES (?,?,?,?,?,?,?,?,?,?)";
$stmt= mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssssssss", $documento, $tipo_documento, $nombre, $apellidos, $correo, $clave, $celular, $genero, $rol, $estado);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success']= "Usuario registrado correctamente";
header("location: ver_clientes.php");
exit();
?>
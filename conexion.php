<?php
date_default_timezone_set('America/Bogota');

$servidor = 'localhost';
$usuario = 'root';
$password = '';
$basededatos = 'hotel_sueño_real';
$puerto = 3306;

$conn = mysqli_connect($servidor, $usuario, $password, $basededatos, $puerto);

if (!$conn) {
    die("La conexion ha fallado: " . mysqli_connect_error());
}

?>
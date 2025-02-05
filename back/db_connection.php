<?php

$server = "localhost";
$user = "root";
$password = "";
$database = "dbcarwash";

$conexion = new mysqli($server, $user, $password, $database);

if ($conexion->connect_error) {
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}else{
    echo "conexión exitosa";
}
?>
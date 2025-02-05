<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Método no permitido.");
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    die("Error: Usuario no autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];
$modelo = $_POST['modelo'];
$marca = $_POST['marca'];
$matricula = $_POST['matricula'];
$tipo = $_POST['tipo'];
$fecha = $_POST['fecha'];
$id_servicio = $_POST['id_servicio'];

echo "ID Usuario: " . $id_usuario . "<br>";

// Insertar vehículo
$stmt = $conn->prepare("INSERT INTO vehiculos (modelo, marca, matricula, tipo, id_usuario) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $modelo, $marca, $matricula, $tipo, $id_usuario);
$stmt->execute();

// Obtener el ID del vehículo recién insertado
$id_vehiculo = $conn->insert_id;
if (!$id_vehiculo) {
    die("Error: No se pudo obtener el ID del vehículo.");
}

// Insertar la reserva
$stmt = $conn->prepare("INSERT INTO turnos (id_usuario, id_vehiculo, id_servicio, fecha) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $id_usuario, $id_vehiculo, $id_servicio, $fecha);

if ($stmt->execute()) {
    echo "Registro exitoso";
} else {
    echo "Error en la reserva: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

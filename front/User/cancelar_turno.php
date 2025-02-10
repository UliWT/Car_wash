<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash"; // Aquí va tu conexión a la BD

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
die("Error de conexión: ". $conn->connect_error);
}

if (!isset($_SESSION['id_usuario'])) {
    die("Usuario no autenticado.");
}

$id_turno = $_POST['id_turno']; 
$id_usuario = $_SESSION['id_usuario']; // Guarda el ID del usuario actual

// Definir la variable en la sesión SQL
$conn->query("SET @current_user_id = $id_usuario");

$sql = "DELETE FROM turnos WHERE id_turno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_turno);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();

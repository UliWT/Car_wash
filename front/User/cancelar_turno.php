<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario está logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuario no autenticado.");
}

$id_turno = $_POST['id_turno'] ?? null;

if (!$id_turno) {
    echo json_encode(['success' => false, 'error' => 'ID de turno no proporcionado']);
    exit;
}

// Actualizar el estado del turno a "Cancelado"
$sql = "UPDATE turnos SET estado = 'Cancelado' WHERE id_turno = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_turno, $id_usuario);  // Asegura que solo el usuario logueado pueda cancelar el turno
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo cancelar el turno']);
}

$stmt->close();
$conn->close();
?>
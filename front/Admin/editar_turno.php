<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_turno = $_POST['id_turno'];
    $id_servicio = $_POST['id_servicio']; // Asegúrate de enviar el id_servicio
    $estado = $_POST['estado'];
    $fecha = $_POST['fecha'];

    // Consulta para actualizar el turno
    $sql = "UPDATE turnos SET id_servicio = ?, estado = ?, fecha = ? WHERE id_turno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $id_servicio, $estado, $fecha, $id_turno);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el turno']);
    }
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>

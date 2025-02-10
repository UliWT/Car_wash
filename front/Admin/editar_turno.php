<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]));
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_turno = $_POST['id_turno'];
    $id_servicio = $_POST['id_servicio'];
    $estado = $_POST['estado'];
    $fecha = $_POST['fecha'];

    // Validar que el estado es correcto
    $estadosPermitidos = ['En Espera', 'En Proceso', 'Listo', 'Cancelado'];
    if (!in_array($estado, $estadosPermitidos)) {
        echo json_encode(['status' => 'error', 'message' => 'Estado no válido']);
        exit;
    }

    // Consulta para actualizar el turno
    $sql = "UPDATE turnos SET id_servicio = ?, estado = ?, fecha = ? WHERE id_turno = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la preparación de la consulta']);
        exit;
    }

    $stmt->bind_param("issi", $id_servicio, $estado, $fecha, $id_turno);

    // Ejecutar la consulta y verificar si se actualizó correctamente
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Turno actualizado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el turno']);
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>

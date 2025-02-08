<?php

header("Content-Type: application/json");

file_put_contents("debug_log.txt", print_r($_POST, true));
echo json_encode(["debug" => $_POST]);
exit;


// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Error en la conexión a la base de datos"]);
    exit;
}

// Obtener y validar datos de la solicitud
$id_turno = filter_input(INPUT_POST, "id_turno", FILTER_VALIDATE_INT);
$fecha = filter_input(INPUT_POST, "fecha", FILTER_SANITIZE_STRING);
$id_servicio = filter_input(INPUT_POST, "servicio", FILTER_VALIDATE_INT);
$estado = filter_input(INPUT_POST, "estado", FILTER_SANITIZE_STRING);

if (!$id_turno || !$fecha || !$id_servicio || !$estado) {
    echo json_encode(["success" => false, "error" => "Datos inválidos o faltantes."]);
    exit;
}

// Query para actualizar los datos
$stmt = $conn->prepare("UPDATE turnos SET fecha = ?, id_servicio = ?, estado = ? WHERE id_turno = ?");
$stmt->bind_param("sisi", $fecha, $id_servicio, $estado, $id_turno);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    error_log("Error en la consulta: " . $stmt->error); // Guarda el error en el log del servidor
    echo json_encode(["success" => false, "error" => "Error al actualizar el turno."]);
}

$stmt->close();
$conn->close();
?>

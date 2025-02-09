<?php
header("Content-Type: application/json");
require 'db.php'; // Aquí va tu conexión a la BD

$id_turno = filter_input(INPUT_POST, "id_turno", FILTER_VALIDATE_INT);

if (!$id_turno) {
    echo json_encode(["success" => false, "error" => "ID de turno inválido."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM turnos WHERE id_turno = ?");
$stmt->bind_param("i", $id_turno);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo cancelar el turno."]);
}

$stmt->close();
$conn->close();

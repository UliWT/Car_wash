<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbcarwash";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Error de conexión"]));
    }

    if (!isset($_POST['id_turno']) || !isset($_POST['fecha']) || !isset($_POST['estado']) || !isset($_POST['servicio'])) {
        die(json_encode(["status" => "error", "message" => "Faltan datos requeridos"]));
    }

    $id_turno = $_POST['id_turno'];
    $nueva_fecha = $_POST['fecha'];
    $nuevo_estado = $_POST['estado'];
    $nuevo_servicio = $_POST['servicio'];  // Campo servicio agregado

    // Actualización de turno, incluyendo el campo servicio
    $stmt = $conn->prepare("UPDATE turnos SET fecha = ?, estado = ?, servicio = ? WHERE id_turno = ?");
    $stmt->bind_param("sssi", $nueva_fecha, $nuevo_estado, $nuevo_servicio, $id_turno);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Turno actualizado correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar el turno"]);
    }

    $stmt->close();
    $conn->close();
}
?>

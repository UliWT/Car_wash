<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Mostrar errores si existen

header('Content-Type: application/json');

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Error en la conexión: " . $conn->connect_error]));
}

// Verificar si llegaron los datos
if (isset($_POST["id_turno"], $_POST["fecha"], $_POST["servicio"], $_POST["estado"])) {
    $id_turno = $_POST["id_turno"];
    $fecha = $_POST["fecha"];
    $servicio = $_POST["servicio"];
    $estado = $_POST["estado"];

    // Query para actualizar los datos
    $stmt = $conn->prepare("UPDATE turnos SET fecha = ?, servicio = ?, estado = ? WHERE id_turno = ?");
    $stmt->bind_param("sssi", $fecha, $servicio, $estado, $id_turno);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Faltan datos en la solicitud."]);
}

$conn->close();
?>

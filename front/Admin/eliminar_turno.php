<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_turno'])) {
    $id_turno = intval($_POST['id_turno']);
    
    $sql = "DELETE FROM turnos WHERE id_turno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_turno);
    
    if ($stmt->execute()) {
        echo "Turno eliminado correctamente.";
    } else {
        echo "Error al eliminar el turno.";
    }

    $stmt->close();
}

$conn->close();
?>
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

// Validar formato de fecha (YYYY-MM-DD)
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) {
    die("Error: Formato de fecha incorrecto.");
}

// Buscar si el vehículo ya existe**
$stmt = $conn->prepare("SELECT id_vehiculo FROM vehiculos WHERE matricula = ?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si el vehículo ya existe, obtener su ID
    $row = $result->fetch_assoc();
    $id_vehiculo = $row['id_vehiculo'];
} else {
    // Si no existe, lo insertamos
    $stmt = $conn->prepare("INSERT INTO vehiculos (modelo, marca, matricula, tipo, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $modelo, $marca, $matricula, $tipo, $id_usuario);
    
    if (!$stmt->execute()) {
        die("Error al registrar el vehículo: " . $stmt->error);
    }
    $id_vehiculo = $conn->insert_id; // Obtener ID del nuevo vehículo
}

$conn->begin_transaction();

try{
//Insertar el turno usando el ID del vehículo**
$stmt = $conn->prepare("INSERT INTO turnos (id_usuario, id_vehiculo, id_servicio, fecha, estado) VALUES (?, ?, ?, ?, 'Nuevo')");
$stmt->bind_param("iiis", $id_usuario, $id_vehiculo, $id_servicio, $fecha);

if ($stmt->execute() && $conn->insert_id) { // Se evalúa insert_id para confirmar que se guardó
    echo "Registro de turno exitoso";
} else {
    echo "Error en la reserva: " . $stmt->error;
}

// Confirmar transacción
$conn->commit();
echo json_encode(["status" => "success", "message" => "Registro de turno exitoso"]);

} catch (Exception $e) {
    // Si hay algún error, revertir los cambios
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}


// Cerrar conexiones
$stmt->close();
$conn->close();
?>

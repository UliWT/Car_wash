<?php
session_start();

// Verificar si el método de solicitud es POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die(json_encode(["status" => "error", "message" => "Método no permitido."]));
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]));
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    die(json_encode(["status" => "error", "message" => "Usuario no autenticado."]));
}

$id_usuario = $_SESSION['id_usuario'];
$modelo = $_POST['modelo'];
$marca = $_POST['marca']; // ID de la marca
$matricula = $_POST['matricula'];
$tipo = $_POST['tipo'];
$fecha = $_POST['fecha'];
$id_servicio = $_POST['id_servicio'];

// Validar formato de fecha (YYYY-MM-DD)
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) {
    die(json_encode(["status" => "error", "message" => "Formato de fecha incorrecto."]));
}

// Verificar que la marca no esté vacía
if (empty($marca)) {
    die(json_encode(["status" => "error", "message" => "Marca no seleccionada."]));
}

// Depuración: Verificar si se recibe correctamente la marca
error_log("ID Marca recibido: " . $marca);

// Verificar si el vehículo ya existe
$stmt = $conn->prepare("SELECT id_vehiculo FROM vehiculos WHERE matricula = ?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si el vehículo ya existe, obtener su ID
    $row = $result->fetch_assoc();
    $id_vehiculo = $row['id_vehiculo'];
} else {
    // Si no existe, insertarlo
    $stmt = $conn->prepare("INSERT INTO vehiculos (modelo, id_marca, matricula, tipo, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissi", $modelo, $marca, $matricula, $tipo, $id_usuario);
    
    if (!$stmt->execute()) {
        die(json_encode(["status" => "error", "message" => "Error al registrar el vehículo: " . $stmt->error]));
    }
    $id_vehiculo = $conn->insert_id; // Obtener ID del nuevo vehículo
}

// Obtener el precio del servicio seleccionado
$stmt = $conn->prepare("SELECT precio FROM servicios WHERE id_servicio = ?");
$stmt->bind_param("i", $id_servicio);
$stmt->execute();
$servicio_result = $stmt->get_result();

if ($servicio_result->num_rows > 0) {
    $row = $servicio_result->fetch_assoc();
    $precio_servicio = $row['precio'];
} else {
    die(json_encode(["status" => "error", "message" => "Servicio no encontrado."]));
}

// Iniciar transacción para asegurar que todo se registre correctamente
$conn->begin_transaction();

try {
    // Insertar el turno usando el ID del vehículo
    $stmt = $conn->prepare("INSERT INTO turnos (id_usuario, id_vehiculo, id_servicio, fecha, estado) VALUES (?, ?, ?, ?, 'En Espera')");
    $stmt->bind_param("iiis", $id_usuario, $id_vehiculo, $id_servicio, $fecha);

    if ($stmt->execute()) {
        // Obtener el ID del turno insertado
        $id_turno = $conn->insert_id;

        // Insertar el pago
        $stmt_pago = $conn->prepare("INSERT INTO pagos (id_usuario, id_turno, monto_total, fecha) VALUES (?, ?, ?, NOW())");
        $stmt_pago->bind_param("iid", $id_usuario, $id_turno, $precio_servicio); // "i" para id_usuario y id_turno, "d" para monto_total

        if ($stmt_pago->execute()) {
            // Confirmar transacción antes de responder
            $conn->commit();
            echo json_encode([
                "status" => "success",
                "message" => "Registro de turno y pago exitoso",
                "precio" => number_format($precio_servicio, 2, ',', '.')
            ]);
        } else {
            // Rollback si el pago no se inserta correctamente
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => "Error al registrar el pago: " . $stmt_pago->error]);
        }
    } else {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Error en la reserva: " . $stmt->error]);
    }
} catch (Exception $e) {
    // Si hay algún error, revertir los cambios
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Cerrar conexiones
$stmt->close();
$stmt_pago->close();
$conn->close();
?>

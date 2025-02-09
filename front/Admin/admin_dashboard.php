<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error en la conexión: " . $conn->connect_error]));
}

// Obtener el filtro de marca, si se ha enviado
$marcaSeleccionada = isset($_GET['marca']) ? $_GET['marca'] : "";

// Consultar la base de datos
$sql = "SELECT t.id_turno, p.nombre AS nombre_usuario, p.apellido AS apellido_usuario, v.matricula AS vehiculo_matricula, 
               s.nombre AS servicio, t.fecha, t.estado, s.precio, v.id_marca 
        FROM turnos t
        JOIN personas p ON t.id_usuario = p.id_usuario
        JOIN vehiculos v ON t.id_vehiculo = v.id_vehiculo
        JOIN servicios s ON t.id_servicio = s.id_servicio
        JOIN marcas m ON v.id_marca = m.id_marcas";  // Asegúrate que la tabla 'marcas' esté correctamente unida

// Filtrar por marca si es necesario
if ($marcaSeleccionada != "") {
    $sql .= " WHERE v.id_marca = " . intval($marcaSeleccionada);  // Usamos v.id_marca en lugar de 'marca_id'
}

$sql .= " ORDER BY t.id_turno DESC";

$result = $conn->query($sql);

$html = "";
$count = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr>";
        $html .= "<td>{$row['id_turno']}</td>";
        $html .= "<td>{$row['nombre_usuario']}</td>";
        $html .= "<td>{$row['apellido_usuario']}</td>";
        $html .= "<td>{$row['vehiculo_matricula']}</td>";
        $html .= "<td>{$row['servicio']}</td>";
        $html .= "<td>{$row['fecha']}</td>";
        $html .= "<td>{$row['estado']}</td>";
        $html .= "<td>$" . number_format($row['precio'], 2, ',', '.') . "</td>"; // Formato de precio
        $html .= "<td>
            <button class='action-btn edit' onclick=\"editarTurno({$row['id_turno']})\">Editar</button>
            <button class='action-btn delete' onclick=\"eliminarTurno({$row['id_turno']})\">Eliminar</button>
                  </td>";
        $html .= "</tr>";
        $count++;
    }
} else {
    $html .= "<tr><td colspan='9'>No hay turnos registrados</td></tr>";
}

$conn->close();

echo json_encode(["html" => $html, "count" => $count]);
?>

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

// Ahora obtenemos el precio desde la tabla servicios
$sql = "SELECT id_turno, nombre_usuario, apellido_usuario, vehiculo_matricula, servicio, fecha, estado, precio 
        FROM vista_turnos 
        JOIN servicios ON vista_turnos.servicio = servicios.nombre
        ORDER BY id_turno DESC";

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

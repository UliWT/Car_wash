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

// 🔹 Agregado `ORDER BY id_turno DESC` para ordenar por ID de turno de manera descendente
$sql = "SELECT id_turno, nombre_usuario, apellido_usuario, vehiculo_matricula, servicio, fecha, estado FROM vista_turnos ORDER BY id_turno DESC";
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
        $html .= "<td>
                    <button class='action-btn edit' onclick=\"showPopup('editPopup')\">Editar</button>
                    <button class='action-btn delete' onclick=\"showPopup('deletePopup')\">Eliminar</button>
                  </td>";
        $html .= "</tr>";
        $count++;
    }
} else {
    $html .= "<tr><td colspan='8'>No hay turnos registrados</td></tr>";
}

$conn->close();

// 🔹 Devolvemos tanto `html` como `count` en la respuesta
echo json_encode(["html" => $html, "count" => $count]); 
?>

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

$sql = "SELECT nombre_usuario, vehiculo_matricula, servicio, fecha, estado FROM vista_turnos";
$result = $conn->query($sql);

$html = "";
$count = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($row["fecha"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["estado"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["vehiculo_matricula"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["nombre_usuario"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["servicio"]) . "</td>";
        $html .= "<td>
                    <button class='action-btn view' onclick=\"showPopup('viewPopup')\">Ver</button>
                    <button class='action-btn edit' onclick=\"showPopup('editPopup')\">Editar</button>
                    <button class='action-btn delete' onclick=\"showPopup('deletePopup')\">Eliminar</button>
                  </td>";
        $html .= "</tr>";
        $count++;
    }
} else {
    $html .= "<tr><td colspan='6'>No hay turnos registrados</td></tr>";
}

$conn->close();

echo json_encode(["html" => $html, "count" => $count]);
?>

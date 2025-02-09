<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]));
}

// Filtrar por marca si está seleccionado
$marcaFiltro = isset($_GET['marca']) && $_GET['marca'] !== '' ? intval($_GET['marca']) : null;

// Consulta actualizada con todas las relaciones correctas
$sql = "SELECT 
            t.id_turno, 
            p.nombre AS nombre_usuario, 
            p.apellido AS apellido_usuario, 
            v.matricula, 
            m.marca, 
            v.modelo, 
            s.nombre AS servicio, 
            t.fecha, 
            t.estado, 
            s.precio
        FROM turnos t
        JOIN personas p ON t.id_usuario = p.id_usuario
        JOIN vehiculos v ON t.id_vehiculo = v.id_vehiculo
        JOIN marcas m ON v.id_marca = m.id_marcas
        JOIN servicios s ON t.id_servicio = s.id_servicio";

if ($marcaFiltro) {
    $sql .= " WHERE v.id_marca = $marcaFiltro";
}

$sql .= " ORDER BY t.id_turno DESC";

$result = $conn->query($sql);

$html = "";
$count = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $count++;
        $html .= "<tr>
                    <td>{$row['id_turno']}</td>
                    <td>{$row['nombre_usuario']}</td>
                    <td>{$row['apellido_usuario']}</td>
                    <td>{$row['matricula']}</td>
                    <td>{$row['marca']}</td>
                    <td>{$row['modelo']}</td>
                    <td>{$row['servicio']}</td>
                    <td>{$row['fecha']}</td>
                    <td>{$row['estado']}</td>
                    <td>\$" . number_format($row['precio'], 2, ',', '.') . "</td>
                    <td>
                        <button class='action-btn edit' onclick=\"editarTurno({$row['id_turno']}, '{$row['fecha']}', '{$row['servicio']}', '{$row['estado']}')\">Editar</button>
                        <button class='action-btn delete' onclick=\"eliminarTurno({$row['id_turno']})\">Eliminar</button>
                    </td>
                </tr>";
    }
} else {
    $html = "<tr><td colspan='11'>No hay turnos registrados</td></tr>";
}

$conn->close();

echo json_encode(["html" => $html, "count" => $count]);
?>

<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la conexión a la base de datos");
}

$sql = "SELECT 
            t.fecha, 
            t.estado, 
            v.matricula, 
            p.nombre AS personas, 
            s.nombre AS servicio, 
            s.precio 
        FROM turnos t
        JOIN vehiculos v ON t.id_vehiculo = v.matricula
        JOIN personas p ON t.id_usuario = p.nombre
        JOIN servicios s ON t.id_servicio = s.precio";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["fecha"] . "</td>";
        echo "<td>" . $row["estado"] . "</td>";
        echo "<td>" . $row["matricula"] . "</td>";
        echo "<td>" . $row["persona"] . "</td>";
        echo "<td>" . $row["servicio"] . "</td>";
        echo "<td>$" . number_format($row["precio"], 2) . "</td>";
        echo "<td>
                <button class='action-btn view' onclick=\"showPopup('viewPopup')\">Ver</button>
                <button class='action-btn edit' onclick=\"showPopup('editPopup')\">Editar</button>
                <button class='action-btn delete' onclick=\"showPopup('deletePopup')\">Eliminar</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No hay turnos registrados</td></tr>";
}

$conn->close();
?>


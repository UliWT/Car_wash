<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "dbcarwash");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL para obtener los montos recaudados, considerando el precio de los servicios
$sql = "SELECT 
            COALESCE(SUM(CASE WHEN p.fecha >= CURDATE() THEN p.monto_total END), 0) AS hoy,
            COALESCE(SUM(CASE WHEN p.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN p.monto_total END), 0) AS ultimo_mes,
            COALESCE(SUM(CASE WHEN p.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN p.monto_total END), 0) AS ultimos_3_meses,
            COALESCE(SUM(CASE WHEN p.fecha >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) THEN p.monto_total END), 0) AS ultimos_6_meses
        FROM pagos p
        JOIN turnos t ON p.id_turno = t.id_turno
        JOIN servicios s ON t.id_servicio = s.id_servicio";

$resultado = $conexion->query($sql);
$datos = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Flujo de Caja</title>
</head>
<body>
    <h2>Resumen de Recaudación</h2>
    <table border="1">
        <tr>
            <th>Hoy</th>
            <th>Último Mes</th>
            <th>Últimos 3 Meses</th>
            <th>Últimos 6 Meses</th>
        </tr>
        <tr>
            <td>$<?php echo number_format($datos['hoy'], 2); ?></td>
            <td>$<?php echo number_format($datos['ultimo_mes'], 2); ?></td>
            <td>$<?php echo number_format($datos['ultimos_3_meses'], 2); ?></td>
            <td>$<?php echo number_format($datos['ultimos_6_meses'], 2); ?></td>
        </tr>
    </table>
</body>
</html>

<?php
$conexion->close();
?>

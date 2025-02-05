<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Envío POST</title>
</head>
<body>
    <h2>Formulario de Prueba - Envío a process_booking.php</h2>
    <form action="process_booking.php" method="POST">
        <label for="id_usuario">ID Usuario:</label>
        <input type="text" name="id_usuario" value="3" required><br><br>

        <label for="modelo">Modelo del Auto:</label>
        <input type="text" name="modelo" value="PruebaModelo" required><br><br>

        <label for="marca">Marca del Auto:</label>
        <input type="text" name="marca" value="PruebaMarca" required><br><br>

        <label for="matricula">Matrícula:</label>
        <input type="text" name="matricula" value="ABC123" required><br><br>

        <label for="tipo">Tipo de Vehículo:</label>
        <select name="tipo" required>
            <option value="Auto" selected>Auto</option>
            <option value="Moto">Moto</option>
            <option value="Camioneta">Camioneta</option>
        </select><br><br>

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" value="2024-02-06" required><br><br>

        <label for="id_servicio">ID Servicio:</label>
        <input type="text" name="id_servicio" value="1" required><br><br>

        <button type="submit">Enviar</button>
    </form>
</body>
</html>

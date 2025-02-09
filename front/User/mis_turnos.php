<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si el usuario está logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuario no autenticado.");
}

// Consulta para obtener los turnos del usuario con información detallada
$sql = "SELECT id_turno, nombre_usuario, apellido_usuario, vehiculo_matricula, servicio, fecha, estado, precio 
        FROM vista_turnos 
        JOIN servicios ON vista_turnos.servicio = servicios.nombre
        WHERE vista_turnos.id_usuario = ?
        ORDER BY id_turno DESC";

// Asegurarse de que $id_usuario esté disponible
if ($id_usuario) {
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario); // 'i' para entero
    $stmt->execute();
    $result = $stmt->get_result();

    $turnos = [];
    while ($row = $result->fetch_assoc()) {
        $turnos[] = $row;
    }

    $stmt->close();
} else {
    echo "Usuario no encontrado o no autenticado.";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Turnos</title>
    <link rel="stylesheet" href="styles-user.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="user-container">
        <header class="header">
            <h1>Mis Turnos</h1>
        </header>
        <main class="main">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Matrícula</th>
                        <th>Servicio</th>
                        <th>Precio</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="turnos-body">
                    <?php if (empty($turnos)): ?>
                        <tr><td colspan="8">No tienes turnos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($turnos as $turno): ?>
                            <tr>
                                <td><?= $turno['id_turno'] ?></td>
                                <td><?= htmlspecialchars($turno['nombre_usuario']) ?></td>
                                <td><?= htmlspecialchars($turno['vehiculo_matricula']) ?></td>
                                <td><?= htmlspecialchars($turno['servicio']) ?></td>
                                <td>$<?= number_format($turno['precio'], 2) ?></td>
                                <td><?= $turno['fecha'] ?></td>
                                <td><?= htmlspecialchars($turno['estado']) ?></td>
                                <td>
                                    <button onclick="editarTurno(<?= $turno['id_turno'] ?>, '<?= $turno['fecha'] ?>', '<?= $turno['servicio'] ?>')">Editar</button>
                                    <button onclick="cancelarTurno(<?= $turno['id_turno'] ?>)">Cancelar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p>Total de turnos: <span id="total-turnos"><?= count($turnos) ?></span></p>
        </main>
    </div>

    <!-- Popup para editar turnos -->
    <div id="editPopup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="cerrarPopup()">&times;</span>
            <h2>Editar Turno</h2>
            <form id="editTurnoForm">
                <input type="hidden" id="edit-id_turno" name="id_turno">
                <label for="edit-fecha">Fecha:</label>
                <input type="date" id="edit-fecha" name="fecha" required>
                <label for="edit-servicio">Servicio:</label>
                <select id="edit-servicio" name="id_servicio" required>
                    <option value="1">Lavado Básico</option>
                    <option value="2">Lavado Completo</option>
                    <option value="3">Lavado Premium</option>
                </select>
                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <form action="../Logout/logout.php" method="POST">
        <button type="submit">Cerrar Sesión</button>
    </form>

    <form action="../User/menu.php">
        <button type="submit">Volver atrás</button>
    </form>  

    <script>
        function editarTurno(id_turno, fecha, servicio) {
            $("#edit-id_turno").val(id_turno);
            $("#edit-fecha").val(fecha);
            $("#edit-servicio").val(servicio);
            $("#editPopup").show();
        }

        function cerrarPopup() {
            $("#editPopup").hide();
        }

        $("#editTurnoForm").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "editar_turno.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        alert("Turno actualizado correctamente.");
                        cerrarPopup();
                        location.reload();
                    } else {
                        alert("Error al actualizar: " + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                    alert("Error al actualizar el turno.");
                }
            });
        });

        function cancelarTurno(id) {
            if (confirm("¿Seguro que deseas cancelar el turno " + id + "?")) {
                $.ajax({
                    url: "cancelar_turno.php",
                    type: "POST",
                    data: { id_turno: id },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            alert("Turno cancelado correctamente.");
                            location.reload();
                        } else {
                            alert("Error al cancelar: " + response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en AJAX:", error);
                        alert("Error al cancelar el turno.");
                    }
                });
            }
        }
    </script>

</body>
</html>

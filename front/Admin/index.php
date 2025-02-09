<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Administrador</title>
    <link rel="stylesheet" href="styles-admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="header">
            <h1>Turnos Agendados</h1>
        </header>
        <main class="main">
            <!-- Filtros -->
            <div class="filters">
                <!-- Filtro de marcas -->
                <div class="filter-container">
                    <label for="marca-filter">Filtrar por marca:</label>
                    <select id="marca-filter" name="marca">
                        <option value="">Seleccione una marca</option>
                        <?php
                        // Conexión a la base de datos para obtener las marcas
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "dbcarwash";
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Error de conexión: " . $conn->connect_error);
                        }

                        // Obtener todas las marcas
                        $marcas_result = $conn->query("SELECT id_marcas, marca FROM marcas");
                        while ($row = $marcas_result->fetch_assoc()) {
                            echo "<option value='{$row['id_marcas']}'>{$row['marca']}</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>

                <!-- Filtro por fecha -->
                <div class="filter-container">
                    <label for="fecha-filter">Filtrar por periodo:</label>
                    <select id="fecha-filter" name="fecha">
                        <option value="">Siempre</option>
                        <option value="1">Último mes</option>
                        <option value="3">Últimos 3 meses</option>
                        <option value="6">Últimos 6 meses</option>
                    </select>
                </div>
            </div>

            <!-- Tabla de turnos -->
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>ID Turno</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Matrícula</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th>Servicio</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="appointments-body">
                    <!-- Se cargan los datos dinámicamente desde admin_dashboard.php -->
                </tbody>
            </table>
            <div class="summary">
                <p>Total Turnos: <span id="total-turnos"></span></p>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cargarTurnos() {
            let marcaSeleccionada = $("#marca-filter").val();
            let periodoSeleccionado = $("#fecha-filter").val();

            $.ajax({
                url: "admin_dashboard.php?marca=" + marcaSeleccionada + "&periodo=" + periodoSeleccionado + "&t=" + new Date().getTime(),
                type: "GET",
                dataType: "json",
                cache: false,
                success: function(response) {
                    if (response.html) {
                        $("#appointments-body").html(response.html);
                        $("#total-turnos").text(response.count);
                    } else {
                        $("#appointments-body").html("<tr><td colspan='11'>No hay turnos registrados</td></tr>");
                        $("#total-turnos").text(0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                }
            });
        }

        $(document).ready(function() {
            cargarTurnos();  // Cargar turnos al inicio

            $("#marca-filter, #fecha-filter").change(function() {
                cargarTurnos();  // Recargar cuando cambia el filtro
            });

            setInterval(cargarTurnos, 2000); // Refrescar cada 2 segundos
        });

        function editarTurno(id_turno, fecha, servicio, estado) {
            $("#edit-id_turno").val(id_turno);
            $("#edit-fecha").val(fecha);
            $("#edit-servicio").val(servicio);
            $("#edit-estado").val(estado);
            $("#editPopup").show();
        }

        function cerrarPopup() {
            $("#editPopup").hide();
        }

        $(document).ready(function() {
            $("#editTurnoForm").submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: "editar_turno.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            alert("Turno actualizado correctamente.");
                            cerrarPopup();
                            cargarTurnos();
                        } else {
                            alert("Error al actualizar: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en AJAX:", error);
                        alert("Error al actualizar el turno.");
                    }
                });
            });
        });

        function eliminarTurno(id) {
            if (confirm("¿Seguro que deseas eliminar el turno " + id + "?")) {
                $.ajax({
                    url: "eliminar_turno.php",
                    type: "POST",
                    data: { id_turno: id },
                    success: function(response) {
                        alert(response);
                        cargarTurnos();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en AJAX:", error);
                    }
                });
            }
        }
    </script>

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
                <input type="text" id="edit-servicio" name="servicio" required>

                <label for="edit-estado">Estado:</label>
                <select id="edit-estado" name="estado">
                    <option value="En Espera">En Espera</option>
                    <option value="En Proceso">En Proceso</option>
                    <option value="Listo">Listo</option>
                    <option value="Entregado">Entregado</option>
                </select>

                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px gray;
            z-index: 1000;
        }

        .filters {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .appointments-table th, .appointments-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .appointments-table th {
            background-color: #f4f4f4;
        }

        .summary {
            margin-top: 20px;
        }

        .summary p {
            font-weight: bold;
        }
    </style>

    <form action="../Logout/logout.php" method="POST">
        <button type="submit">Cerrar Sesión</button>
    </form>
</body>
</html>

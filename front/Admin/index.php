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

            <!-- Tabla de turnos -->
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>ID Turno</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Matrícula</th>
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
            // Obtener el valor seleccionado del filtro
            let marcaSeleccionada = $("#marca-filter").val();
            
            // Enviar la solicitud AJAX para cargar los turnos filtrados
            $.ajax({
                url: "admin_dashboard.php?marca=" + marcaSeleccionada + "&t=" + new Date().getTime(),
                type: "GET",
                dataType: "json",
                cache: false,
                success: function(response) {
                    if (response.html) {
                        $("#appointments-body").html(response.html);
                        $("#total-turnos").text(response.count);
                    } else {
                        $("#appointments-body").html("<tr><td colspan='9'>No hay turnos registrados</td></tr>");
                        $("#total-turnos").text(0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                }
            });
        }

        $(document).ready(function() {
            // Cargar los turnos al cargar la página
            cargarTurnos();
            
            // Configurar el filtro de marca
            $("#marca-filter").change(function() {
                cargarTurnos(); // Recargar los turnos cuando se seleccione una marca
            });

            setInterval(cargarTurnos, 2000); // Refrescar cada 2 segundos
        });

        function editarTurno(id_turno, fecha, servicio, estado) {
            // Llenar el formulario del popup con los datos actuales del turno
            $("#edit-id_turno").val(id_turno);
            $("#edit-fecha").val(fecha);
            $("#edit-servicio").val(servicio);
            $("#edit-estado").val(estado);

            // Mostrar el popup de edición
            $("#editPopup").show();
        }

        function cerrarPopup() {
            $("#editPopup").hide();
        }

        $(document).ready(function() {
            $("#editTurnoForm").submit(function(event) {
                event.preventDefault(); // Evita la recarga de la página

                $.ajax({
                    url: "editar_turno.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        console.log("Respuesta del servidor:", response);
                        if (response.status === "success") {
                            alert("Turno actualizado correctamente.");
                            cerrarPopup(); // Cierra el popup al actualizar correctamente
                            cargarTurnos(); // Recargar la lista de turnos
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
    </style>
</body>
</html>

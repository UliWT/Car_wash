<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Administrador</title>
    <link rel="stylesheet" href="styles-admin.css">
</head>
<body>
    <nav class="navbar">
        <a href="../User/menu.php">Inicio</a>
        <form action="../Logout/logout.php" method="POST" class="logout-form">
            <button type="submit">Cerrar Sesión</button>
        </form>
    </nav>

    <div class="admin-container">
        <header class="header">
            <h1>Turnos Agendados</h1>
        </header>
        <main class="main">
            <!-- Filtro de marcas -->
            <div class="filter-container">
    <label for="marca-filter">Filtrar por marca:</label>
    <select id="marca-filter" name="marca">
        <option value="">Todas</option>
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

    <label for="periodo-filter">Filtrar por período:</label>
    <select id="periodo-filter" name="periodo">
        <option value="">Todos</option>
        <option value="1">Último mes</option>
        <option value="3">Últimos 3 meses</option>
        <option value="6">Últimos 6 meses</option>
        <option value="12">Último año</option>
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
                <p class="total-turnos">Total Turnos: <span id="total-turnos"></span></p>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function cargarTurnos() {
            let marcaSeleccionada = $("#marca-filter").val();
            let periodoSeleccionado = $("#periodo-filter").val();

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

            $("#marca-filter, #periodo-filter").change(function() {
                cargarTurnos();  // Recargar cuando cambia el filtro
            });

            setInterval(cargarTurnos, 2000); // Refrescar cada 2 segundos
        });

        function editarTurno(id_turno, fecha, servicio, estado) {
            $("#edit-id_turno").val(id_turno);
            $("#edit-fecha").val(fecha);
            $("#edit-estado").val(estado);

            // Cargar servicios desde admin_dashboard.php
            $.ajax({
                url: "admin_dashboard.php?get_servicios=1",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $("#edit-servicio").empty();
                    response.servicios.forEach(function(serv) {
                        $("#edit-servicio").append(
                            `<option value="${serv.id_servicio}" ${serv.id_servicio == servicio ? "selected" : ""}>${serv.nombre}</option>`
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                }
            });

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
    <div id="editPopup" class=".popup">
        <div class="popup-content">
            <span class="close-btn" onclick="cerrarPopup()">&times;</span>
            <h2>Editar Turno</h2>
            <form id="editTurnoForm">
                <input type="hidden" id="edit-id_turno" name="id_turno">

                <label for="edit-fecha">Fecha:</label>
                <input type="date" id="edit-fecha" name="fecha" required>

                <label for="edit-servicio">Servicio:</label>
                <select id="edit-servicio" name="id_servicio" required></select>

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
        
.filter-container {
display: flex;
align-items: center;
justify-content: flex-start;
gap: 15px;
margin-bottom: 20px;
background-color: #f9f9f9;
padding: 10px;
border-radius: 8px;
box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.filter-container label {
font-weight: bold;
color: #333;
margin-right: 5px;
}

.filter-container select {
padding: 8px;
border: 1px solid #ccc;
border-radius: 5px;
font-size: 1rem;
background-color: #fff;
color: #333;
}

.filter-container select:hover {
border-color: #3fada8;
}

.filter-container button {
padding: 8px 12px;
background-color: #3fada8;
color: white;
border: none;
border-radius: 5px;
font-size: 1rem;
cursor: pointer;
transition: background-color 0.3s ease;
}

.filter-container button:hover {
background-color: #358e85;
}

.filter-container button:active {
background-color: #2d7a72;
transform: scale(0.98);
}  
.popup {
    display: none; /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%; /* Adjust as needed */
    max-width: 500px; /* Set a maximum width */
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    z-index: 1000; /* Ensure it's on top */
}
/* Contenido del popup */
.popup-content {
background: #ffffff;
padding: 20px 30px;
border-radius: 12px;
width: 90%;
max-width: 500px;
position: relative;
box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
animation: fadeIn 0.3s ease-in-out;
}

/* Animación de entrada */
@keyframes fadeIn {
from {
    opacity: 0;
    transform: scale(0.9);
}
to {
    opacity: 1;
    transform: scale(1);
}
}

/* Botón para cerrar el popup */
.close-btn {
position: absolute;
top: 10px;
right: 10px;
font-size: 18px;
font-weight: bold;
color: #555;
cursor: pointer;
}

.close-btn:hover {
color: #f44336;
}

/* Encabezado del popup */
.popup-content h2 {
font-size: 1.5rem;
margin-bottom: 20px;
color: #333;
text-align: center;
}

/* Estilos de los formularios dentro del popup */
#editTurnoForm {
display: flex;
flex-direction: column;
gap: 15px;
}

#editTurnoForm label {
font-size: 0.9rem;
color: #555;
}

#editTurnoForm input,
#editTurnoForm select {
width: 100%;
padding: 8px 12px;
border: 1px solid #ccc;
border-radius: 8px;
font-size: 1rem;
}

#editTurnoForm button {
background: #3fada8;
color: #fff;
font-weight: bold;
padding: 10px 15px;
border: none;
border-radius: 8px;
cursor: pointer;
transition: background 0.3s ease-in-out;
}

#editTurnoForm button:hover {
background: #329d91;
}
    </style>
</body>
</html>
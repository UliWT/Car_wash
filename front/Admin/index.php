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
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: gray;
   }
   
   .admin-container {
    padding: 20px;
   }
   
   table {
    background-color: white;
    border: 2px solid black;
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
   }
   
   th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
   }
   
   .header {
    background-color: #3fada8;
    color: white;
    border: 3px solid black;
    padding: 15px;
    text-align: center;
   }
   
   .action-btn {
    padding: 5px 10px;
    margin: 0 2px;
    cursor: pointer;
    color: white;
   }
   
   .action-btn.view { background-color: #4CAF50; }
   .action-btn.edit { background-color: #FFC107; }
   .action-btn.delete { background-color: #F44336; }
   
   .popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    padding: 20px;
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
   }
   
   .popup.active {
    display: block;
   }
   
   .popup-header {
    font-weight: bold;
    margin-bottom: 15px;
   }
   
   .popup-actions {
    text-align: right;
   }
   
   .popup-actions button {
    padding: 5px 10px;
    margin: 0 2px;
   }
   
   .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
   }
   
   .overlay.active {
    display: block;
   }
   
   .turnos{
    background-color: #3fada8;
    border: 1px solid black;
   }

   .navbar {
       display: flex;
       justify-content: space-between;
       align-items: center;
       padding: 10px 20px;
       background-color: #3fada8;
       border-bottom: 2px solid black;
   }

   .navbar a {
       color: white;
       text-decoration: none;
       font-weight: bold;
   }

   .logout-form {
       margin: 0;
   }

   .logout-form button {
       background-color: #f44336;
       color: white;
       border: none;
       padding: 8px 12px;
       cursor: pointer;
   }

   .logout-form button:hover {
       background-color: #d32f2f;
   }

   .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #3fada8;
    border-bottom: 2px solid black;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.logout-form {
    margin: 0;
}

.logout-form button {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
}

.logout-form button:hover {
    background-color: #d32f2f;
}
.total-turnos {
font-size: 1.2em;
color: #333;
text-align: center;
background-color: #f0f8ff;
padding: 10px;
border: 1px solid #ddd;
border-radius: 8px;
margin-top: 20px;
}

#total-turnos {
font-size: 1.2em;
color: #333;
text-align: center;
background-color: #3fada8;
padding: 10px;
border: 1px solid #ddd;
border-radius: 8px;
margin-top: 20px;
}
    </style>
</body>
</html>
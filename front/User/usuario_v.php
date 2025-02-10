<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigir a la página de inicio de sesión
    header("Location: ../Login/Login.html");
    exit();
}

// Obtener el ID del usuario de la sesión
$id_usuario = $_SESSION['id_usuario'];

// Conexión a la base de datos para obtener las marcas
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$marcas_result = $conn->query("SELECT id_marcas, marca FROM marcas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="menu.php">
            <i class="fas fa-home"></i> Inicio
        </a>
        <form action="../Logout/logout.php" method="POST">
            <button type="submit">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </form>
    </div>

    <div class="user-container">
        <header class="header">
            
            <h1>Selecciona un servicio y agenda tu turno</h1>
        </header>
        <main class="main">
            <div class="service-cards">
                <div class="card" onclick="openForm('3')">
                    <img src="../resources/complete.png" alt="Lavado Completo">
                    <h2>Lavado Completo y Detailing</h2>
                    <p>Incluye limpieza interior y exterior.</p>
                    <p class="price">$100,000</p>
                </div>
                <div class="card" onclick="openForm('2')">
                    <img src="../resources/exterior.png" alt="Lavado Exterior">
                    <h2>Lavado Exterior</h2>
                    <p>Incluye lavado y encerado.</p>
                    <p class="price">$60,000</p>
                </div>
                <div class="card" onclick="openForm('1')">
                    <img src="../resources/interior.png" alt="Limpieza Interior">
                    <h2>Limpieza Interior</h2>
                    <p>Aspirado y limpieza profunda.</p>
                    <p class="price">$50,000</p>
                </div>
            </div>
            <button class="schedule-btn" id="schedule-btn"><div id="letra"></div></button>
        </main>

        <!-- Formulario emergente -->
        <div class="form-popup" id="form-popup">
            <form id="booking-form" action="../../front/process_booking.php" method="POST" onsubmit="submitForm(event)">
                <h2>Agendar Turno</h2>

                <label for="modelo">Modelo del Auto:</label>
                <input type="text" id="modelo" name="modelo" required>

                <label for="marca">Marca del Auto:</label>
                <select name="marca" id="marca" required>
                    <option value="">Selecciona una marca</option>
                    <?php while ($row = $marcas_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id_marcas']; ?>"><?php echo $row['marca']; ?></option>
                    <?php } ?>
                </select>

                <label for="matricula">Matricula:</label>
                <input type="text" id="matricula" name="matricula" required>

                <label for="tipo">Tipo de vehiculo:</label>
                <select name="tipo" id="tipo">
                    <option value="Auto">Auto</option>
                    <option value="Moto">Moto</option>
                    <option value="Camioneta">Camioneta</option>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>

                <input type="hidden" id="id_servicio" name="id_servicio">
                <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">

                <button type="submit">Guardar Turno</button>
                <button type="button" onclick="closeForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function openForm(id_servicio) {
            document.getElementById("schedule-btn").style.display = "none";
            document.getElementById("form-popup").style.display = "block";
            document.getElementById("id_servicio").value = id_servicio;
        }

        function closeForm() {
            document.getElementById("schedule-btn").style.display = "block";
            document.getElementById("form-popup").style.display = "none";
            document.getElementById("booking-form").reset();
        }

        function submitForm(event) {
            event.preventDefault();

            // Obtener el ID de usuario de PHP
            let idUsuario = "<?php echo $_SESSION['id_usuario']; ?>";

            // Asignarlo al campo oculto antes de enviarlo
            document.getElementById("id_usuario").value = idUsuario;

            // Crear FormData
            const formData = new FormData(document.getElementById("booking-form"));

            fetch("../User/process_booking.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.text())
            .then(text => {
                if (text.toLowerCase().includes("exitoso")) {
                    alert("Turno guardado exitosamente.");
                    window.location.href = "mis_turnos.php";
                } else {
                    alert("Error al guardar el turno: " + text);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Hubo un problema con el envío del turno.");
            });
        }
    </script>
</body>
</html>

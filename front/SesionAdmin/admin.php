<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión a la base de datos");
}

// Recoger datos del formulario
$correo = $_POST['correo'];
$contrasena = md5($_POST['contrasena']); // Hashear la contraseña

// Consulta para verificar si el administrador existe en la base de datos
$sql = "SELECT * FROM administradores WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $correo, $contrasena);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró un administrador con las credenciales
if ($result->num_rows > 0) {
    // El administrador ha iniciado sesión correctamente
    session_start();
    $_SESSION['admin_logged_in'] = true; // Guardar sesión de administrador
    header("Location: ../Admin/admin_dashboard.html"); // Redirigir al dashboard
    exit();
} else {
    // Si no se encuentra el administrador, mostrar un error
    echo "<div class='message error'>Correo o contraseña incorrectos.</div>";
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>

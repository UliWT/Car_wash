
<?php
// Iniciar sesión
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto por tu usuario de MySQL
$password = ""; // Cambia esto por tu contraseña de MySQL
$dbname = "dbcarwash";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos del formulario
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Consulta para verificar el usuario en la tabla "personas"
$sql = "SELECT id_usuario, nombre, contrasena, rol FROM personas WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuario encontrado, verificar contraseña
    $row = $result->fetch_assoc();

    if (md5($contrasena) === $row['contrasena']) { // Comparar contraseñas encriptadas con MD5
        // Guardar usuario en sesión
        $_SESSION['id_usuario'] = $row['id_usuario']; // Guardar el ID en la sesión
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['rol'] = $row['rol'];

        // Redirigir al usuario según su rol
        if ($row['rol'] === 'admin') {
            header("Location: ../SeionAdmin/admin.html");
        } else {
            header("Location: ../User/usuario_v.php");
        }
        exit();
    } else {
        echo "<div class='message error'>Contraseña incorrecta.</div>";
    }
} else {
    echo "<div class='message error'>Usuario no encontrado.</div>";
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>


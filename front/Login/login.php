<?php
// Iniciar sesión
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbcarwash";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos del formulario
$correo = trim($_POST['correo']);
$contrasena = trim($_POST['contrasena']);

// Encriptar la contraseña con MD5
$hashed_password = md5($contrasena);

// Consulta para verificar el usuario en la tabla "personas"
$sql = "SELECT id_usuario, nombre, contrasena, rol FROM personas WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Usuario encontrado, verificar contraseña
    $row = $result->fetch_assoc();

    if ($hashed_password === $row['contrasena']) { // Comparar hash MD5
        // Guardar usuario en sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['nombre'] = $row['nombre'];
        $_SESSION['rol'] = $row['rol'];

        // Redirigir al usuario según su rol
        if ($row['rol'] === 'admin') {
            header("Location: ../Admin/index.html");
        } else {
            header("Location: ../User/usuario_v.php");
        }
        exit();
    } else {
        echo "Contraseña Incorrecta";
    }
} else {
    echo "<div class='message error'>Usuario no encontrado.</div>";
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>

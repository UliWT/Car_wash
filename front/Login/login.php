<?php
// Iniciar sesión para manejar autenticaciones y variables de sesión
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "dbcarwash";

// Crear conexión con la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variable para almacenar mensajes de error (para mostrar en el popup)
$mensaje = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Preparar la consulta para buscar el usuario en la base de datos
    $sql = "SELECT id_usuario, nombre, contrasena, rol FROM personas WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró un usuario con el correo ingresado
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Obtener los datos del usuario

        // Comparar la contraseña ingresada con la almacenada en la base de datos (encriptada con MD5)
        if (md5($contrasena) === $row['contrasena']) {
            // Guardar datos del usuario en la sesión
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['rol'] = $row['rol'];

            // Redirigir al usuario según su rol
            if ($row['rol'] === 'admin') {
                header("Location: ../SesionAdmin/admin.html");
            } else {
                header("Location: ../User/usuario_v.php");
            }
            exit(); // Detener la ejecución del script después de la redirección
        } else {
            // Si la contraseña es incorrecta, se almacena un mensaje de error
            $mensaje = "Contraseña incorrecta";
        }
    } else {
        // Si el correo no está registrado, se almacena un mensaje de error
        $mensaje = "Usuario no encontrado";
    }

    // Cerrar la consulta preparada
    $stmt->close();
}

// Cerrar la conexión con la base de datos
$conn->close();
?>
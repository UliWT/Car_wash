
<?php
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
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$direccion = $_POST['direccion'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$contrasena = md5($_POST['contrasena']); // Encriptar la contraseña

// Preparar y ejecutar la consulta SQL
$sql = "INSERT INTO personas (nombre, apellido, direccion, correo, telefono, contrasena)
VALUES ('$nombre', '$apellido', '$direccion', '$correo', '$telefono', '$contrasena')";

if ($conn->query($sql) === TRUE) {
    echo "Registro exitoso";
    header("Location: ../Login/Login.html");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar conexión
$conn->close();
?>
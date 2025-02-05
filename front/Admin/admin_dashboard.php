<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "dbcarwash";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la conexión a la base de datos");
}

$email = "admin@lavadero.com";
$password = md5("admin123"); // Hashear la contraseña

$sql = "INSERT INTO administradores (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Administrador agregado correctamente.";
} else {
    echo "Error al agregar administrador.";
}

$stmt->close();
$conn->close();
?>

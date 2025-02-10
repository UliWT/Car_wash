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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Usuario</title>
    <link rel="stylesheet" href="styles-menu.css"> <!-- Conexión con CSS -->
    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="index.php">
        <i class="fas fa-home"></i> Inicio
    </a>
    <form action="../Logout/logout.php" method="POST">
        <button type="submit">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
    </form>
</div>

<!-- Contenedor de las opciones con imágenes -->
<div class="container">
    <div class="option" id="Block1">
        <a href="usuario_v.php">
            <img src="registrar.png" alt="Registrar un Turno">
            <p>Registrar un Turno</p>
        </a>
    </div>

    <div class="option" id="Block2">
        <a href="mis_turnos.php">
            <img src="ver.png" alt="Ver mis Turnos">
            <p>Ver mis Turnos</p>
        </a>
    </div>
</div>

</body>
</html>
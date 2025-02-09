<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Usuario</title>
    <link rel="stylesheet" href="styles.css"> <!-- Conexión con CSS -->
</head>
<body>

<div class="navbar">
    <a href="index.php">Inicio</a>
    
    <div class="dropdown">
        <a href="#">Turnos ▼</a>
        <div class="dropdown-content">
            <a href="usuario_v.php">Registrar un Turno</a>
            <a href="mis_turnos.php">Ver mis Turnos</a>
        </div>
    </div>

    <form action="../Logout/logout.php" method="POST">
        <button type="submit">Cerrar Sesión</button>
    </form>
</div>

</body>
</html>

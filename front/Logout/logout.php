<?php
session_start(); // Iniciar sesión
session_destroy(); // Destruir la sesión
header("Location: ../Login/Login.html"); // Redirigir al login
exit();
?>
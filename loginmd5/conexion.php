<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuario_db = "root"; // Tu usuario de MySQL
$password_db = "";    // Tu contraseña de MySQL
$nombre_db = "login_bootstrap_md5";

// Crear conexión
$conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

// Verificar conexión
if ($conexion->connect_error) {
    die("La conexión ha fallado: " . $conexion->connect_error);
}

// Opcional: Establecer el juego de caracteres a UTF-8
$conexion->set_charset("utf8mb4");
?>
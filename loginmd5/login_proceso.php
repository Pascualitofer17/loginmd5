<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión
require 'conexion.php';

// Verificar si se enviaron datos por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener y limpiar los datos del formulario
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $password = $_POST['password'];

    // Encriptar la contraseña ingresada con MD5 para compararla con la de la base de datos
    $password_md5 = md5($password);

    // Preparar la consulta para evitar inyecciones SQL básicas
    $stmt = $conexion->prepare("SELECT id, usuario FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->bind_param("ss", $usuario, $password_md5);

    // Ejecutar la consulta
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si se encontró un usuario
    if ($resultado->num_rows === 1) {
        // Usuario autenticado correctamente
        $datos_usuario = $resultado->fetch_assoc();
        
        // Guardar datos del usuario en la sesión
        $_SESSION['usuario_id'] = $datos_usuario['id'];
        $_SESSION['usuario_nombre'] = $datos_usuario['usuario'];

        // Redirigir a la página de bienvenida
        header("Location: bienvenida.php");
        exit();
    } else {
        // Credenciales incorrectas
        $_SESSION['error_login'] = "Usuario o contraseña incorrectos.";
        
        // Redirigir de vuelta al formulario de login
        header("Location: index.php");
        exit();
    }

    // Cerrar la sentencia y la conexión
    $stmt->close();
    $conexion->close();
} else {
    // Si alguien intenta acceder directamente a este archivo, lo redirigimos
    header("Location: index.php");
    exit();
}
?>
<?php
// Iniciar sesión para acceder a las variables de sesión
session_start();

// Verificar si el usuario ha iniciado sesión
// Si la variable de sesión 'usuario_id' no existe, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Mi Sistema</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">¡Bienvenido de nuevo!</h4>
            <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>. Has iniciado sesión correctamente.</p>
            <hr>
            <p class="mb-0">Esta es una página protegida. Solo los usuarios autenticados pueden verla.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
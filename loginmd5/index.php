<?php
// Iniciar la sesión para poder manejar mensajes de error
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card shadow-sm login-card">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">Iniciar Sesión</h2>
                
                <?php
                // Mostrar mensaje de error si existe
                if (isset($_SESSION['error_login'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_login'] . '</div>';
                    // Eliminar el mensaje después de mostrarlo para que no aparezca de nuevo
                    unset($_SESSION['error_login']);
                }
                ?>

                <form action="login_proceso.php" method="POST">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required autocomplete="off">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Ingresar</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <small class="text-muted">Usuario: admin | Contraseña: 12345</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
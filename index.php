<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Archivos PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <?php
        // Obtener el directorio de la URL o usar el directorio actual si no se especifica
        $dir_actual = isset($_GET['dir']) ? $_GET['dir'] : '.';

        // Limpiar la ruta para evitar problemas de seguridad
        $dir_actual = str_replace(['..', './'], '', $dir_actual);

        // Si la ruta es inválida, volver al directorio raíz
        if (!is_dir($dir_actual)) {
            $dir_actual = '.';
        }

        // Obtener la ruta del directorio padre
        $dir_padre = dirname($dir_actual);

        // Obtener el protocolo y el host del servidor
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/');

        // Lista de extensiones de archivos potencialmente ejecutables
        $extensiones_bloqueadas = array('php', 'php3', 'php4', 'php5', 'phtml', 'html', 'htm', 'cgi', 'pl', 'py', 'js', 'sh');
        ?>

        <h1 class="mb-4 text-center">Contenido del Directorio: <?php echo htmlspecialchars($dir_actual); ?></h1>

        <?php if ($dir_actual !== '.') : ?>
            <p><a href="?dir=<?php echo urlencode($dir_padre); ?>" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left me-2"></i>Volver a la carpeta anterior</a></p>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col" class="text-center">Acción</th>
                        <th scope="col">Enlace directo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Usamos scandir() para obtener un array de archivos y directorios
                    $items = scandir($dir_actual);

                    // Filtramos las entradas '.' y '..' para no mostrarlas
                    $items = array_diff($items, array('.', '..'));

                    // Obtener el nombre del script actual para no mostrarlo
                    $script_actual = basename($_SERVER['PHP_SELF']);

                    if (!empty($items)) {
                        foreach ($items as $item) {
                            // Omitir el archivo del script actual en el directorio raíz
                            if ($dir_actual === '.' && $item === $script_actual) {
                                continue;
                            }
                            
                            $ruta_completa = $dir_actual . DIRECTORY_SEPARATOR . $item;
                            $enlace_directo = $protocol . $domainName . $base_url . '/' . $ruta_completa;
                            $enlace_navegacion = '';

                            echo "<tr>";
                            
                            // Determinar el ícono y si el elemento es ejecutable
                            $extension = pathinfo($item, PATHINFO_EXTENSION);
                            $es_ejecutable = in_array(strtolower($extension), $extensiones_bloqueadas);
                            $icono = '';

                            if (is_dir($ruta_completa)) {
                                $icono = "<i class='bi bi-folder-fill text-warning me-2'></i>";
                                $enlace_navegacion = "?dir=" . urlencode($ruta_completa);
                                $accion_html = "<a href='{$enlace_navegacion}' class='btn btn-primary btn-sm'>Acceder</a>";
                                $enlace_directo_html = "<td><a href='{$enlace_directo}' target='_blank' class='btn btn-light btn-sm' title='Abrir en nueva pestaña'><i class='bi bi-box-arrow-up-right'></i></a></td>";
                            } elseif (is_file($ruta_completa)) {
                                if($es_ejecutable){
                                    $icono = "<i class='bi bi-x-octagon-fill text-danger me-2'></i>";
                                    $accion_html = "<button class='btn btn-secondary btn-sm' disabled>Acceder</button>";
                                } else {
                                    $icono = "<i class='bi bi-file-earmark-code-fill text-info me-2'></i>";
                                    $enlace_navegacion = $ruta_completa;
                                    $accion_html = "<a href='{$enlace_navegacion}' class='btn btn-primary btn-sm'>Acceder</a>";
                                }
                                $enlace_directo_html = "<td><a href='{$enlace_directo}' target='_blank' class='btn btn-light btn-sm' title='Abrir en nueva pestaña'><i class='bi bi-box-arrow-up-right'></i></a></td>";
                            } else {
                                $icono = "<i class='bi bi-question-circle-fill me-2'></i>";
                                $accion_html = "<button class='btn btn-secondary btn-sm' disabled>Acceder</button>";
                                $enlace_directo_html = "<td><button class='btn btn-light btn-sm' disabled><i class='bi bi-slash-circle'></i></button></td>";
                            }
                            
                            // Columna Nombre (con el ícono integrado)
                            echo "<td>{$icono} {$item}</td>";
                            
                            // Columna con el botón de "Acceder"
                            echo "<td class='text-center'>{$accion_html}</td>";
                            
                            // Columna Enlace Directo
                            echo "{$enlace_directo_html}";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No se encontraron archivos ni carpetas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNWqjA2MUDrL+0qYhWp7m0tL3tqG4Tz2q6U5fC" crossorigin="anonymous"></script>
</body>
</html>
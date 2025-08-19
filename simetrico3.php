<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrador y Descifrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Cifrador de Mensajes Simple</h1>
        <p class="text-center">Usa este formulario para cifrar o descifrar un mensaje.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Introduce tu mensaje:</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required><?php echo isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="accion" class="form-label">Elige una acción:</label>
                                <select class="form-select" id="accion" name="accion">
                                    <option value="cifrar" <?php echo (isset($_POST['accion']) && $_POST['accion'] == 'cifrar') ? 'selected' : ''; ?>>Cifrar</option>
                                    <option value="descifrar" <?php echo (isset($_POST['accion']) && $_POST['accion'] == 'descifrar') ? 'selected' : ''; ?>>Descifrar</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Lógica de PHP para procesar el formulario
        
        // Clave de sustitución
        $mi_clave = [
            'a' => '00', 'b' => '01', 'c' => '02', 'd' => '03', 'e' => '04', 'f' => '05', 'g' => '06',
            'h' => '07', 'i' => '08', 'j' => '09', 'k' => '10', 'l' => '11', 'm' => '12',
            'n' => '13', 'o' => '14', 'p' => '15', 'q' => '16', 'r' => '17', 's' => '18',
            't' => '19', 'u' => '20', 'v' => '21', 'w' => '22', 'x' => '23', 'y' => '24',
            'z' => '25'
        ];
        
        // Funciones de cifrado y descifrado
        function cifrar_mensaje($mensaje, $clave) {
            $mensaje_cifrado = "";
            $caracteres = str_split($mensaje);
            foreach ($caracteres as $caracter) {
                $caracter_minuscula = strtolower($caracter);
                if (array_key_exists($caracter_minuscula, $clave)) {
                    $mensaje_cifrado .= $clave[$caracter_minuscula];
                } else {
                    $mensaje_cifrado .= $caracter;
                }
            }
            return $mensaje_cifrado;
        }

        function descifrar_mensaje($mensaje_cifrado, $clave) {
            $clave_inversa = array_flip($clave);
            $traduccion = [];
            foreach ($clave_inversa as $valor => $letra) {
                $traduccion[$valor] = strtoupper($letra);
            }
            return strtr($mensaje_cifrado, $traduccion);
        }

        // Verificamos si el formulario ha sido enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mensaje_original = $_POST['mensaje'];
            $accion = $_POST['accion'];
            $resultado = "";

            if ($accion == 'cifrar') {
                $resultado = cifrar_mensaje($mensaje_original, $mi_clave);
                $titulo_resultado = "Mensaje Cifrado:";
            } elseif ($accion == 'descifrar') {
                $resultado = descifrar_mensaje($mensaje_original, $mi_clave);
                $titulo_resultado = "Mensaje Descifrado:";
            }

            // Mostramos el resultado
            if (!empty($resultado)) {
                echo '<div class="mt-4 p-3 bg-light border rounded">';
                echo '<h4>' . $titulo_resultado . '</h4>';
                echo '<p class="lead">' . htmlspecialchars($resultado) . '</p>';
                echo '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
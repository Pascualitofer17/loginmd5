<?php

// --- INICIO DEL PROCESAMIENTO PHP ---

// 1. Inicializar variables para evitar errores
$texto_cifrado_b64 = '';
$clave_ingresada = '';
$iv_hex = '';
$texto_plano_original = '';
$error_mensaje = '';

// 2. Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Obtener los datos del formulario de forma segura
    $texto_plano = isset($_POST['mensaje']) ? $_POST['mensaje'] : '';
    $clave = isset($_POST['clave']) ? $_POST['clave'] : '';

    // Guardamos las variables para mostrarlas después en el formulario
    $texto_plano_original = $texto_plano;
    $clave_ingresada = $clave;

    // --- MEJORA DE SEGURIDAD IMPORTANTE ---
    // AES-256-CBC necesita una clave de exactamente 32 bytes (256 bits).
    // Una clave corta como "123" es insegura y causará un error.
    // Usamos hash('sha256') para convertir cualquier clave de usuario
    // en un hash seguro de 32 bytes. El 'true' es para salida binaria.
    $clave_segura = hash('sha256', $clave, true);

    // 4. Generar un Vector de Inicialización (IV) seguro y único para cada cifrado
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($iv_length);

    // 5. Cifrar el texto plano
    // Usamos OPENSSL_RAW_DATA para obtener la salida en bruto (raw) y tener más control.
    // Luego, la codificaremos en Base64 manualmente.
    $texto_cifrado_raw = openssl_encrypt(
        $texto_plano,
        'aes-256-cbc',
        $clave_segura,
        OPENSSL_RAW_DATA, // Opción para obtener datos en bruto
        $iv
    );
    
    // Es una práctica común anteponer el IV al texto cifrado antes de codificarlo.
    // Esto facilita el descifrado, ya que el IV necesario está junto con el mensaje.
    $texto_cifrado_b64 = base64_encode($iv . $texto_cifrado_raw);
    
    // Convertimos el IV a hexadecimal solo para mostrarlo de forma legible
    $iv_hex = bin2hex($iv);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado Simétrico AES-256</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .font-monospace {
            word-wrap: break-word; /* Asegura que las cadenas largas no rompan el layout */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-3">Cifrado Simétrico con Bootstrap y PHP</h2>
            <p class="mb-4">Introduce un mensaje y una clave secreta para realizar el cifrado usando el algoritmo <strong>AES-256-CBC</strong>.</p>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="mb-3">
                            <label for="mensaje" class="form-label"><strong>Mensaje a Cifrar:</strong></label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="3" required><?php echo htmlspecialchars($texto_plano_original); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label"><strong>Clave Secreta:</strong></label>
                            <input type="password" class="form-control" id="clave" name="clave" value="<?php echo htmlspecialchars($clave_ingresada); ?>" required>
                            <div class="form-text">Tu clave puede ser de cualquier longitud. Será procesada con SHA-256 para garantizar la longitud requerida de 32 bytes de forma segura.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cifrar Mensaje</button>
                    </form>
                </div>
            </div>

            <?php if (!empty($texto_cifrado_b64)): ?>
                <div class="card mt-5">
                    <div class="card-header">
                        <h3>Resultados del Cifrado</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Vector de Inicialización (IV) generado (en hexadecimal):</strong></label>
                            <p class="font-monospace p-2 bg-light border rounded"><?php echo $iv_hex; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Texto Cifrado (IV + Mensaje) en Base64:</strong></label>
                            <textarea class="form-control font-monospace" rows="5" readonly><?php echo $texto_cifrado_b64; ?></textarea>
                            <div class="form-text">Este es el texto final que puedes almacenar o transmitir de forma segura.</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Generamos un par de claves (pública y privada) solo si no existen
// En un entorno real, las claves se generarían una vez y se almacenarían de forma segura.
if (!file_exists('private.pem') || !file_exists('public.pem')) {
    // Creamos la configuración para las claves
    $config = [
        "digest_alg" => "sha512",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    // Generamos las claves
    $res = openssl_pkey_new($config);
    openssl_pkey_export($res, $privateKey);

    // Obtenemos la clave pública del recurso de claves
    $publicKey = openssl_pkey_get_details($res)['key'];

    // Guardamos las claves en archivos
    file_put_contents('private.pem', $privateKey);
    file_put_contents('public.pem', $publicKey);
} else {
    // Si ya existen, las cargamos de los archivos
    $publicKey = file_get_contents('public.pem');
    $privateKey = file_get_contents('private.pem');
}

// Lógica para descifrar el mensaje del formulario
$decryptedMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encrypted_message'])) {
    $encryptedMessage = base64_decode($_POST['encrypted_message']);

    // Desciframos el mensaje usando la clave privada
    openssl_private_decrypt($encryptedMessage, $decryptedMessage, $privateKey);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado Asimétrico con PHP y Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Ejemplo de Cifrado Asimétrico</h1>
    <p>Este ejemplo usa una clave pública para cifrar un mensaje en el navegador y una clave privada en el servidor para descifrarlo.</p>

    <div class="card p-4">
        <h3 class="mb-3">Cifrar un Mensaje</h3>
        <div class="mb-3">
            <label for="message" class="form-label">Mensaje a Cifrar:</label>
            <textarea class="form-control" id="message" rows="3"></textarea>
        </div>
        <button class="btn btn-primary" onclick="encryptMessage()">Cifrar y Enviar</button>
        <div class="mt-3">
            <p><strong>Mensaje Cifrado (Frontend):</strong></p>
            <textarea class="form-control" id="encryptedMessageFrontend" rows="3" readonly></textarea>
        </div>
    </div>

    <hr class="my-5">

    <div class="card p-4 bg-light">
        <h3 class="mb-3">Mensaje Descifrado (Backend)</h3>
        <?php if ($decryptedMessage): ?>
            <p><strong>Mensaje Original:</strong></p>
            <p class="alert alert-success"><?= htmlspecialchars($decryptedMessage) ?></p>
        <?php else: ?>
            <p class="text-muted">Esperando un mensaje cifrado...</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function encryptMessage() {
        const message = document.getElementById('message').value;

        // La clave pública es generada por PHP y está disponible en el DOM
        // En una aplicación real, no se expondría directamente en el HTML.
        const publicKey = `<?= addcslashes($publicKey, "\r\n") ?>`;

        // Creamos un nuevo objeto de cifrado
        const JSEncrypt = new JSEncrypt();
        JSEncrypt.setPublicKey(publicKey);

        // Ciframos el mensaje
        const encrypted = JSEncrypt.encrypt(message);

        // Mostramos el mensaje cifrado en el frontend
        document.getElementById('encryptedMessageFrontend').value = encrypted;

        // Creamos un formulario dinámico para enviar el mensaje cifrado al backend
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'encrypted_message';
        input.value = encrypted;
        form.appendChild(input);
        document.body.appendChild(form);
        
        // Enviamos el formulario
        form.submit();
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsencrypt/3.3.2/jsencrypt.min.js"></script>

</body>
</html>
<?php
/**
 * Lógica del servidor (PHP)
 * Generación y gestión de claves asimétricas con phpseclib v3.
 */

// Incluimos el autoloader de phpseclib para cargar las clases.
// Usamos __DIR__ para asegurar una ruta absoluta y correcta.
require_once __DIR__ . '/phpseclib-master/phpseclib/bootstrap.php';

use phpseclib3\Crypt\RSA;

// Generamos un par de claves (pública y privada) solo si no existen.
if (!file_exists('private_phpseclib.key') || !file_exists('public_phpseclib.key')) {
    try {
        // Generamos las claves con phpseclib.
        $rsa = RSA::createKey();

        // Exportamos la clave privada y la pública en formato PKCS8.
        $privateKey = $rsa->toString('PKCS8');
        $publicKey = $rsa->getPublicKey()->toString('PKCS8');

        // Guardamos las claves en archivos.
        file_put_contents('private_phpseclib.key', $privateKey);
        file_put_contents('public_phpseclib.key', $publicKey);
    } catch (\Exception $e) {
        die("Error al generar las claves con phpseclib: " . $e->getMessage());
    }
} else {
    // Si ya existen, las cargamos de los archivos.
    $privateKey = file_get_contents('private_phpseclib.key');
    $publicKey = file_get_contents('public_phpseclib.key');
}

// Variables para el cifrado y descifrado
$plainText = '';
$encryptedText = '';
$decryptedText = '';

// Lógica del servidor para procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se envió un mensaje para cifrar
    if (isset($_POST['message_to_encrypt'])) {
        $plainText = $_POST['message_to_encrypt'];

        try {
            // Cargamos la clave pública y ciframos el mensaje.
            $rsaPublicKey = RSA::load($publicKey);
            $encryptedText = $rsaPublicKey->encrypt($plainText);
            $encryptedText = base64_encode($encryptedText); // Codificamos para mostrar en el HTML
        } catch (\Exception $e) {
            $encryptedText = 'Error al cifrar: ' . $e->getMessage();
        }
    }
    
    // Si se envió un mensaje cifrado para descifrar
    if (isset($_POST['message_to_decrypt'])) {
        $encryptedText = $_POST['message_to_decrypt'];

        try {
            // Cargamos la clave privada y desciframos el mensaje.
            $rsaPrivateKey = RSA::load($privateKey);
            $decryptedText = $rsaPrivateKey->decrypt(base64_decode($encryptedText));
        } catch (\Exception $e) {
            $decryptedText = 'Error al descifrar. Verifique el mensaje cifrado: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrado Asimétrico con phpseclib</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-top: 20px;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        textarea, input[type="text"] {
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result-box {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Cifrado Asimétrico con phpseclib</h1>
    <p>Este ejemplo usa la librería **phpseclib** (versión 3), que no requiere extensiones de PHP, para cifrar y descifrar mensajes.</p>

    <h2>1. Cifrar Mensaje</h2>
    <form action="index.php" method="POST">
        <label for="message_to_encrypt">Mensaje a cifrar:</label>
        <textarea id="message_to_encrypt" name="message_to_encrypt" rows="4"></textarea>
        <button type="submit">Cifrar</button>
    </form>
    
    <?php if ($encryptedText): ?>
        <h2>Mensaje Cifrado</h2>
        <div class="result-box">
            <p><strong>Clave Pública utilizada:</strong><br><pre>...</pre></p>
            <p><strong>Mensaje Cifrado:</strong><br><?= htmlspecialchars($encryptedText) ?></p>
        </div>
    <?php endif; ?>

    <hr>

    <h2>2. Descifrar Mensaje</h2>
    <form action="index.php" method="POST">
        <label for="message_to_decrypt">Mensaje cifrado para descifrar:</label>
        <textarea id="message_to_decrypt" name="message_to_decrypt" rows="4"></textarea>
        <button type="submit">Descifrar</button>
    </form>
    
    <?php if ($decryptedText): ?>
        <h2>Mensaje Descifrado</h2>
        <div class="result-box">
            <p><strong>Mensaje Original:</strong><br><?= htmlspecialchars($decryptedText) ?></p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
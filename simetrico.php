<?php

// Definir una clave secreta y un vector de inicialización (IV)
// La clave debe tener la longitud adecuada para el algoritmo de cifrado.
// En este caso, AES-256-CBC requiere una clave de 32 bytes (256 bits).
$clave = "123";

// El IV (Initialization Vector) debe ser de 16 bytes para AES-256-CBC.
// Es importante que el IV sea único para cada operación de cifrado.
//openssl_random_pseudo_bytes() genera un IV aleatorio y seguro.
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

// Texto que se quiere cifrar
$texto_plano = "Hola";

// --- PASO 1: CIFRADO ---
// Se utiliza openssl_encrypt para cifrar el texto
$texto_cifrado = openssl_encrypt(
    $texto_plano,            // El texto que se va a cifrar
    'aes-256-cbc',           // El algoritmo de cifrado (AES con 256 bits y modo CBC)
    $clave,                  // La clave secreta
    0,                       // Opciones: 0 significa que se devuelve como base64 por defecto.
    $iv                       // El vector de inicialización
);







// --- PASO 2: ALMACENAMIENTO O TRANSMISIÓN ---
// Para descifrar, se necesitan tanto el texto cifrado como el IV original.
// Es común concatenarlos para su transmisión o almacenamiento.
$mensaje_final = base64_encode($iv . $texto_cifrado);

echo "Texto plano: " . $texto_plano . "\n";
echo "Texto cifrado (base64): " . $mensaje_final . "\n";

// --- PASO 3: DESCIFRADO ---
// Primero se decodifica el mensaje final para separar el IV del texto cifrado.
$mensaje_decodificado = base64_decode($mensaje_final);

// Extraer el IV (los primeros 16 bytes)
$iv_obtenido = substr($mensaje_decodificado, 0, openssl_cipher_iv_length('aes-256-cbc'));

// Extraer el texto cifrado restante
$texto_cifrado_obtenido = substr($mensaje_decodificado, openssl_cipher_iv_length('aes-256-cbc'));

// Se utiliza openssl_decrypt para descifrar el texto
$texto_descifrado = openssl_decrypt(
    $texto_cifrado_obtenido,  // El texto cifrado a descifrar
    'aes-256-cbc',            // El mismo algoritmo
    $clave,                   // La misma clave
    0,                        // Las mismas opciones
    $iv_obtenido              // El mismo IV
);

echo "Texto descifrado: " . $texto_descifrado . "\n";

?>
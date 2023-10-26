<?php

// Obtener la actualización del bot de Telegram
$update = json_decode(file_get_contents("php://input"), true);

// Verificar si el mensaje es un comando
if (isset($update["message"]["text"])) {
    $command = $update["message"]["text"];
    
    // Verificar si el comando es /gen seguido de un valor
    if (strpos($command, '/gen') === 0) {
        // Obtener el valor proporcionado después de /gen
        $valorProporcionado = trim(substr($command, strlen('/gen')));

        // Generar y validar una tarjeta con el nuevo valor
        $numeroTarjeta = generarNumero($valorProporcionado);
        $mensajeRespuesta = "¡Aquí está tu tarjeta generada y validada: " . $numeroTarjeta . "!";
        enviarMensajeTelegram($update["message"]["chat"]["id"], $mensajeRespuesta);
    } else {
        // Manejar otros comandos si es necesario
        enviarMensajeTelegram($update["message"]["chat"]["id"], "Comando no reconocido");
    }
}

// Función para enviar mensajes a Telegram
function enviarMensajeTelegram($chatId, $mensaje) {
    $url = "https://api.telegram.org/botTU_TOKEN_AQUI/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($mensaje);
    file_get_contents($url);
}

function generarNumero($patron) {
    $numero = '';

    for ($i = 0; $i < strlen($patron); $i++) {
        $caracter = $patron[$i];

        if ($caracter === 'x') {
            $numero .= mt_rand(0, 9);
        } elseif (is_numeric($caracter)) {
            $numero .= $caracter;
        }
    }

    // Completar con dígitos aleatorios si es necesario para alcanzar la longitud de 16 dígitos
    while (strlen($numero) < 16) {
        $numero .= mt_rand(0, 9);
    }

    return $numero;
}

function luhnCheck($number) {
    $number = strrev(preg_replace('/[^\d]/', '', $number));
    $sum = 0;

    for ($i = 0, $j = strlen($number); $i < $j; $i++) {
        $digit = (int) $number[$i];

        if ($i % 2 === 1) {
            $digit *= 2;

            if ($digit > 9) {
                $digit -= 9;
            }
        }

        $sum += $digit;
    }

    return $sum % 10 === 0;
}
?>

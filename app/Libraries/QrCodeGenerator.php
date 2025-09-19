<?php

namespace App\Libraries;

class QrCodeGenerator {

    public function generateDailyQrData($user_id) {
        $date = date('Y-m-d');
        $salt = QR_SALT; // Usar el SALT definido

        // Generar el hash de validación diario
        $validation_hash = hash('sha256', $user_id . $date . $salt);
        
        // El contenido del QR será: ID_Usuario|Hash_de_validación
        $qr_content = $user_id . '|' . $validation_hash;
        
        return $qr_content;
    }

    public function validateDailyQrData($qr_data)
    {
        $parts = explode('|', $qr_data);
        
        // El QR debe tener el formato ID|HASH
        if (count($parts) !== 2) {
            return false;
        }

        $user_id = $parts[0];
        $scanned_hash = $parts[1];

        // Calcular el hash esperado para el usuario y la fecha actual
        $date = date('Y-m-d');
        $salt = QR_SALT; // Usar la constante definida, no $this->salt
        $expected_hash = hash('sha256', $user_id . $date . $salt);

        // Comparar el hash escaneado con el hash esperado
        return $scanned_hash === $expected_hash;
    }
}
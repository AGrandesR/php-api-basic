<?php

namespace Private\Utils;

class Cryptor {
    static function encrypt(array|string $data, $encryptionKey) {
        // Convert the data to JSON
        $jsonData = json_encode($data);
        
        // Generate a random initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        
        // Encrypt the data using AES-256-CBC
        $encryptedData = openssl_encrypt($jsonData, 'aes-256-cbc', $encryptionKey, 0, $iv);
        
        // Create an array with the initialization vector and the encrypted data
        $encryptedJson = [
            'iv' => base64_encode($iv),
            'data' => $encryptedData
        ];

        // Convert the array to JSON
        $finalJson = json_encode($encryptedJson);

        return base64_encode($finalJson);
    }
    
    static function decrypt(string $encrypt, $encryptionKey) {
        // Decode the encrypted JSON
        $encryptedString = base64_decode($encrypt);

        $encryptedJson = json_decode($encryptedString,true);
        
        // Decode the initialization vector
        $iv = base64_decode($encryptedJson['iv']);
        
        // Decrypt the data using AES-256-CBC
        $decryptedData = openssl_decrypt($encryptedJson['data'], 'aes-256-cbc', $encryptionKey, 0, $iv);
        
        // Decode the decrypted JSON
        $data = json_decode($decryptedData, true);
        
        return $data;
    }
}
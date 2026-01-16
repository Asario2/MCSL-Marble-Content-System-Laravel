<?php

namespace App\Http\Controllers;

class EncryptController extends Controller
{
    const AES_256_CBC = 'aes-256-cbc';

    private $_secret_key;
    private $_secret_iv;
    private $_encryption_key;
    private $_iv;

    public function __construct()
    {
        $hash = env('APP_HASHCODE', "SECRET");

        $this->_secret_key = 'x1os318cmgDFGept0qeQuDhj7aunfwyleqeuc' . $hash;
        $this->_secret_iv  = 'nwqfbweqlm1QWEna20tQDwm72i7x52h4bw19y' . $hash;

        $this->_encryption_key = hash('sha256', $this->_secret_key);
        $this->_iv             = substr(hash('sha256', $this->_secret_iv), 0, 16);
    }

    /**
     * String verschlüsseln
     */
    public function encryptString($data)
    {
        if (!is_string($data)) {
            return $data; // nur Strings verschlüsseln
        }

        $encrypted = openssl_encrypt($data, self::AES_256_CBC, $this->_encryption_key, 0, $this->_iv);

        return $encrypted !== false ? base64_encode($encrypted) : $data;
    }

    /**
     * String entschlüsseln
     */
    public function decryptString($data)
    {

        if (!is_string($data)) {
            return $data; // nicht-Strings gar nicht anfassen
        }

        $decoded = base64_decode($data, true);

        if ($decoded === false || $decoded === NULL) {
            return $data; // kein gültiges Base64 → unverändert zurück
        }

        $decrypted = openssl_decrypt($decoded, self::AES_256_CBC, $this->_encryption_key, 0, $this->_iv);

        // 🔑 Wichtig: nie false zurückgeben, immer einen String
        \Log::info("DC".$decoded);
        return $decrypted !== false && $decrypted !== '' ? $decrypted : $data;
    }



            public function setEncryptionKey($key)
            {
            $this->_encryption_key = $key;
            }

            public function setInitVector($iv)
            {
            $this->_iv = $iv;
            }

        }

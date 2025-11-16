<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OldEnc extends Controller
{
    //

 // Konstante f�r Verschl�sselungsmethode
  const AES_256_CBC = 'aes-256-cbc';

  private $_secret_key = 'x1os318cmgDFGept0qeQuDhj7aunfwyleqeuc'; // hier einen sicheren Schl�ssel einsetzen
  private $_secret_iv  = 'nwqfbweqlm1QWEna20tQDwm72i7x52h4bw19y'; // hier einen weiteren sicheren Schl�ssel einsetzen
  private $_encryption_key;
  private $_iv;

  // im Konstruktor werden die Instanzvariablen initialisiert
  public function __construct()
  {
    @define('_fruxel_wyweexq',null);
    include "_ab/inc/fertu/bcfg.php";
    $hash = $hfcg['enchash'];
    unset($hfcg['enchash']);
    $this->_secret_key = 'x1os318cmgDFGept0qeQuDhj7aunfwyleqeuc'.@$hash; // hier einen sicheren Schl�ssel einsetzen
    $this->_secret_iv  = 'nwqfbweqlm1QWEna20tQDwm72i7x52h4bw19y'.@$hash;
    $this->_encryption_key = hash('sha256', $this->_secret_key);
    $this->_iv             = substr(hash('sha256', $this->_secret_iv), 0, 16);
  }

  public function encryptString($data)
  {
    if(@is_array($data))
    {
      return $data;

    }
    return @base64_encode(openssl_encrypt($data, self::AES_256_CBC, $this->_encryption_key, 0, $this->_iv));
  }

  public function decryptString($data)
  {
    return @openssl_decrypt(@base64_decode(@$data), self::AES_256_CBC, $this->_encryption_key, 0, $this->_iv);
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
?>

<?php
function decryptAES($cipherText)
{
    // Base64 decode
    $key = config('app.aes_key');
    $cipherData = base64_decode($cipherText);

    // CryptoJS adds the salt with prefix "Salted__" and 8 bytes salt
    $salted = substr($cipherData, 0, 8);
    $salt = substr($cipherData, 8, 8);
    $ct = substr($cipherData, 16);

    // Derive key and IV from password and salt using OpenSSL EVP_BytesToKey method
    $keyAndIV = EVP_BytesToKey($key, $salt);
    $decrypted = openssl_decrypt($ct, 'aes-256-cbc', $keyAndIV['key'], OPENSSL_RAW_DATA, $keyAndIV['iv']);

    return $decrypted;
}

function EVP_BytesToKey($password, $salt, $key_len = 32, $iv_len = 16)
{
    $dtot = '';
    $d = '';
    while (strlen($dtot) < ($key_len + $iv_len)) {
        $d = md5($d . $password . $salt, true);
        $dtot .= $d;
    }

    return [
        'key' => substr($dtot, 0, $key_len),
        'iv' => substr($dtot, $key_len, $iv_len)
    ];
}

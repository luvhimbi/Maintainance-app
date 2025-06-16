<?php

// Set SSL certificate path
$certPath = __DIR__ . '/../cacert.pem';
if (!file_exists($certPath)) {
    // Download the certificate if it doesn't exist
    $certContent = file_get_contents('https://curl.se/ca/cacert.pem');
    file_put_contents($certPath, $certContent);
}

// Set the SSL certificate path for PHP
ini_set('curl.cainfo', $certPath);
ini_set('openssl.cafile', $certPath);

// Set default SSL context options
$defaultContext = stream_context_get_default([
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true,
        'cafile' => $certPath
    ]
]); 
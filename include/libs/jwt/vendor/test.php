<?php
header('Content-Type: application/json');
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
// JWT secret
define('JWT_SECRET', 'this-is-the-secret');

$issuedAt = time();
$expirationTime = $issuedAt + 60;  // jwt valid for 60 seconds from the issued time
$payload = array(
  'userid' => $userid,
  'iat' => $issuedAt,
  'exp' => $expirationTime
);
$key = JWT_SECRET;
$alg = 'HS256';
echo $jwt = JWT::encode($payload, $key, $alg);
?>

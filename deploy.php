<?php
$secret = 'Haqq123@@%%';
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature)) {
  exec('cd /home/u123456789/public_html && git pull origin master');
}
?>
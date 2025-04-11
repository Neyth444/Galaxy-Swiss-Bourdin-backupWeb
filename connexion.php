<?php
$host = 'localhost';
$db   = 'bisounours';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die(json_encode([
        'success' => false,
        'error' => 'Erreur connexion BDD: ' . $e->getMessage()
    ]));
}
?>

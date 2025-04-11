<?php
header("Content-Type: application/json");
require 'connexion.php';

$fname = $_POST['fname'] ?? null;
$lname = $_POST['lname'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$role = $_POST['role'] ?? null;

if (!$fname || !$lname || !$email || !$password || !$role) {
    echo json_encode(["success" => false, "message" => "Champs requis manquants"]);
    exit;
}

// Hash du mot de passe
$hashed = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO user (fname, lname, email, password, role)
        VALUES (:fname, :lname, :email, :password, :role)
    ");
    $stmt->execute([
        ':fname' => $fname,
        ':lname' => $lname,
        ':email' => $email,
        ':password' => $hashed,
        ':role' => $role
    ]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>

<?php
header("Content-Type: application/json");

// Connexion MySQL
$mysqli = new mysqli("localhost", "root", "", "bisounours");

if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "error" => "Erreur de connexion à la base"]);
    exit;
}

// Vérifie les paramètres POST
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode(["success" => false, "error" => "Champs manquants"]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

// Préparation de la requête
$stmt = $mysqli->prepare("SELECT id_user, password, role FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Vérifie le mot de passe hashé
    if (password_verify($password, $user['password'])) {

        // Détermine le libellé du type à partir du rôle
        $typeLibelle = match ((int)$user['role']) {
            1 => "visiteur",
            2 => "comptable",
            3 => "admin",
            default => "inconnu"
        };

        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user['id_user'],
                "role" => (int)$user['role'],
                "type" => $typeLibelle
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Mot de passe incorrect"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Utilisateur introuvable"]);
}
?>

<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "bisounours");
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "error" => "Connexion échouée"]);
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "error" => "ID manquant"]);
    exit();
}

$stmt = $mysqli->prepare("SELECT id_user, lname, fname, email FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "user" => $user]);
} else {
    echo json_encode(["success" => false, "error" => "Utilisateur non trouvé"]);
}
?>

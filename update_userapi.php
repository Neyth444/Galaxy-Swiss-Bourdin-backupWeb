<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "bisounours");
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "error" => "Connexion échouée"]);
    exit();
}

$id = $_POST['id_user'] ?? null;
$nom = $_POST['nom'] ?? null;
$prenom = $_POST['prenom'] ?? null;

if (!$id || !$nom || !$prenom) {
    echo json_encode(["success" => false, "error" => "Paramètres manquants"]);
    exit();
}

$stmt = $mysqli->prepare("UPDATE user SET nom = ?, prenom = ? WHERE id_user = ?");
$stmt->bind_param("ssi", $nom, $prenom, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Échec de la mise à jour"]);
}
?>

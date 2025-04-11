<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "bisounours");
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "error" => "Connexion échouée"]);
    exit();
}

$id_user = $_POST['id_user'] ?? null;

if (!$id_user) {
    echo json_encode(["success" => false, "error" => "Paramètre id_user manquant"]);
    exit();
}

$stmt = $mysqli->prepare("SELECT id_fiche, date, commentaire FROM fiche WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$fiches = [];

while ($row = $result->fetch_assoc()) {
    $fiches[] = $row;
}

if (count($fiches) > 0) {
    echo json_encode(["success" => true, "fiches" => $fiches]);
} else {
    echo json_encode(["success" => false, "error" => "Aucune fiche trouvée"]);
}
?>

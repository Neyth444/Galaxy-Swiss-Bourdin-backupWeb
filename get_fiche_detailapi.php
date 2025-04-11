<?php
header("Content-Type: application/json");
$mysqli = new mysqli("localhost", "root", "", "bisounours");

if (!$mysqli) {
    echo json_encode(["success" => false, "error" => "Connexion échouée"]);
    exit;
}

$id_fiche = $_GET['id_fiche'] ?? null;

if (!$id_fiche) {
    echo json_encode(["success" => false, "error" => "ID fiche manquant"]);
    exit;
}

$stmt = $mysqli->prepare("
    SELECT tf.type AS type_frais, lf.prix_unitaire, lf.quantite, lf.total_frais
    FROM ligne_frais lf
    INNER JOIN type_frais tf ON lf.id_typefrais = tf.id_lf
    WHERE lf.id_fiche = ?
");

$stmt->bind_param("i", $id_fiche);
$stmt->execute();
$result = $stmt->get_result();

$frais = [];
$totalGlobal = 0;

while ($row = $result->fetch_assoc()) {
    $frais[] = $row;
    $totalGlobal += $row['total_frais'];
}

echo json_encode([
    "success" => true,
    "frais" => $frais,
    "total" => $totalGlobal
]);
?>

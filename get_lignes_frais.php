<?php
header("Content-Type: application/json");


$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_fiche = $_POST['id_fiche'] ?? null;

    if (!$id_fiche) {
        echo json_encode([
            "success" => false,
            "message" => "Paramètre id_fiche manquant"
        ]);
        exit;
    }

    $stmt = $connexion->prepare("
        SELECT l.id_lf, l.id_typefrais, t.type AS libelle, l.quantite, l.prix_unitaire
        FROM ligne_frais l
        INNER JOIN type_frais t ON l.id_typefrais = t.id_lf
        WHERE l.id_fiche = :id_fiche
    ");
    $stmt->bindParam(':id_fiche', $id_fiche);
    $stmt->execute();

    $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "lignes" => $lignes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur : " . $e->getMessage()
    ]);
}
?>

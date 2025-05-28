<?php
header("Content-Type: application/json");

$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupère et décode le JSON brut
    $json = file_get_contents("php://input");
    $donnees = json_decode($json, true);

    if (!isset($donnees["lignes"])) {
        echo json_encode([
            "success" => false,
            "message" => "Données manquantes"
        ]);
        exit;
    }

    foreach ($donnees["lignes"] as $ligne) {
        $stmt = $connexion->prepare("
            UPDATE ligne_frais
            SET quantite = :quantite, prix_unitaire = :prix
            WHERE id_lf = :id
        ");
        $stmt->execute([
            ":quantite" => $ligne["quantite"],
            ":prix" => $ligne["prix_unitaire"],
            ":id" => $ligne["id_lf"]
        ]);
    }

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur : " . $e->getMessage()
    ]);
}
?>

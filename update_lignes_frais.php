<?php
// précise que la réponse sera en json
header("Content-Type: application/json");

// config connexion bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // connexion à la bdd via pdo + gestion des erreurs activée
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // récup du contenu brut (json) depuis php://input
    $json = file_get_contents("php://input");

    // décodage json en array php
    $donnees = json_decode($json, true);

    // vérif que la clé "lignes" existe dans les données
    if (!isset($donnees["lignes"])) {
        echo json_encode([
            "success" => false,
            "message" => "Données manquantes"
        ]);
        exit;
    }

    // boucle sur chaque ligne reçue pour faire la maj
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

    // réponse json si tout ok
    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    // renvoie erreur si exception pdo
    echo json_encode([
        "success" => false,
        "message" => "Erreur : " . $e->getMessage()
    ]);
}
?>

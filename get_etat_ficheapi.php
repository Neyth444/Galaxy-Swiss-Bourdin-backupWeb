<?php
header("Content-Type: application/json");

$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Lire id_user depuis POST
    $id_user = $_POST['id_user'] ?? null;

    if (!$id_user) {
        echo json_encode([
            "success" => false,
            "message" => "Paramètre id_user manquant"
        ]);
        exit;
    }

    $stmt = $connexion->prepare("
        SELECT f.id_fiche, f.date, f.commentaire, f.etat AS etat,
               SUM(l.quantite * l.prix_unitaire) AS total_frais
        FROM fiche f
        LEFT JOIN ligne_frais l ON f.id_fiche = l.id_fiche
        WHERE f.id_user = :id_user
        GROUP BY f.id_fiche
        ORDER BY f.date DESC
    ");
    $stmt->bindParam(':id_user', $id_user);
    $stmt->execute();

    $fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "fiches" => $fiches
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur : " . $e->getMessage()
    ]);
}
?>

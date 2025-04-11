<?php
header("Content-Type: application/json");
require 'connexion.php'; // Le fichier doit fournir $pdo

$id_fiche = $_POST['id_fiche'] ?? $_GET['id_fiche'] ?? null;
$etat = $_POST['status'] ?? $_GET['status'] ?? null; // ⚠️ 'status' devient 'etat'
$commentaire = $_POST['commentaireComptable'] ?? $_GET['commentaireComptable'] ?? "";

if (!$id_fiche || !$etat) {
    echo json_encode([
        "success" => false,
        "message" => "Paramètres manquants"
    ]);
    exit;
}

// Tous les traitements sont à faire ici
try {
    $sql = "
        UPDATE fiche
        SET etat = :etat,
            status = 'Traité', -- statut fixé automatiquement
            commentaireComptable = :commentaire
        WHERE id_fiche = :id_fiche
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':etat', $etat);
    $stmt->bindParam(':commentaire', $commentaire);
    $stmt->bindParam(':id_fiche', $id_fiche, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Fiche mise à jour avec succès"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Échec de la mise à jour"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur : " . $e->getMessage()
    ]);
}
?>

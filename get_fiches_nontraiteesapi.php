<?php
header("Content-Type: application/json");
require 'connexion.php'; // ⚠️ Ce fichier doit définir la variable $pdo (et non $mysqli)

try {
    $sql = "
    SELECT f.id_fiche, f.date, f.commentaire, f.status, f.commentaireComptable, u.fname, u.lname
    FROM fiche f
    JOIN user u ON f.id_user = u.id_user
    WHERE LOWER(f.status) = 'non traité' AND u.role = 1
    ORDER BY f.date_enregistrement DESC
    ";

    $stmt = $pdo->prepare($sql); // ✅ utilisation de PDO
    $stmt->execute();
    $fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "fiches" => $fiches
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>

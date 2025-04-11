<?php
header("Content-Type: application/json");
require 'connexion.php';

try {
    // Total global des frais
    $stmt1 = $pdo->prepare("SELECT SUM(total_frais) AS total FROM ligne_frais");
    $stmt1->execute();
    $totalFrais = $stmt1->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Répartition par type de frais (corrigé avec JOIN)
    $stmt2 = $pdo->prepare("
        SELECT tf.type AS type_frais, SUM(lf.total_frais) AS montant 
        FROM ligne_frais lf
        JOIN type_frais tf ON lf.id_typefrais = tf.id_lf
        GROUP BY tf.type
    ");
    $stmt2->execute();
    $repartition = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Liste des utilisateurs avec leurs rôles (JOIN avec table 'role')
    $stmt3 = $pdo->prepare("
        SELECT u.id_user, u.fname, u.lname, u.email, u.role, r.role AS role_label
        FROM user u
        JOIN role r ON u.role = r.id_role
    ");
    $stmt3->execute();
    $users = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total_frais" => $totalFrais,
        "repartition" => $repartition,
        "utilisateurs" => $users
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>

<?php
// définit le type de contenu en json
header("Content-Type: application/json");

// importe la connexion à la bdd
require 'connexion.php';

// récupère l'id depuis les params GET, sinon null par défaut
$id = $_GET['id'] ?? null;

// si aucun id, retourne une erreur en json et stoppe le script
if (!$id) {
    echo json_encode(["success" => false, "message" => "ID manquant"]);
    exit;
}

try {
    // prépare la requête pour supprimer un user avec un id précis
    $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = :id");
    
    // lie la valeur :id avec la vraie donnée, en mode entier
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // exécute la requête
    $stmt->execute();

    // retour json si suppression ok
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    // en cas d’erreur, retourne un message d’erreur en json
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>

<?php
header('Content-Type: application/json');
require 'connexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id_lf, type FROM type_frais ORDER BY type ASC");

    $types = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $types[] = [
            "id" => $row["id_lf"],       // Correspond à la vraie colonne
            "libelle" => $row["type"]    // Correspond à la vraie colonne
        ];
    }

    echo json_encode([
        "success" => true,
        "types" => $types
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur de connexion : " . $e->getMessage()
    ]);
}
?>
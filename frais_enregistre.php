<?php
include 'db_connection.php'; // fichier de connexion à la base de données

$date = $_POST['date'];
$montant = $_POST['montant'];
$description = $_POST['description'];
$type = $_POST['type'];

// Insertion dans la table fichefrais pour chaque frais
$query_fichefrais = "INSERT INTO fichefrais (date, montant, description) VALUES ('$date', '$montant', '$description')";
mysqli_query($conn, $query_fichefrais);
$fiche_id = mysqli_insert_id($conn);

if ($type == 'forfait') {
    // Insertion dans la table fraisforfait
    $query_fraisforfait = "INSERT INTO fraisforfait (fiche_id, montant) VALUES ('$fiche_id', '$montant')";
    mysqli_query($conn, $query_fraisforfait);

    // Insertion dans la table lignefraisforfait
    $query_lignefraisforfait = "INSERT INTO lignefraisforfait (fiche_id, date, montant) VALUES ('$fiche_id', '$date', '$montant')";
    mysqli_query($conn, $query_lignefraisforfait);
} else {
    // Insertion dans la table lignefraishorsforfait
    $query_lignefraishorsforfait = "INSERT INTO lignefraishorsforfait (fiche_id, date, montant, description) VALUES ('$fiche_id', '$date', '$montant', '$description')";
    mysqli_query($conn, $query_lignefraishorsforfait);
}

echo "Frais enregistré avec succès !";
mysqli_close($conn);
?>

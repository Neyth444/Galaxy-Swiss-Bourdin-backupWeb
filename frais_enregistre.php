<?php
// inclut le fichier de connexion à la bdd
include 'db_connection.php';

// récup données envoyées via formulaire
$date = $_POST['date'];
$montant = $_POST['montant'];
$description = $_POST['description'];
$type = $_POST['type'];

// insert la fiche frais principale
$query_fichefrais = "INSERT INTO fichefrais (date, montant, description) VALUES ('$date', '$montant', '$description')";
mysqli_query($conn, $query_fichefrais);

// récup id de la fiche qui vient d’être insérée
$fiche_id = mysqli_insert_id($conn);

// si type = forfait, insert dans les tables liées au forfait
if ($type == 'forfait') {
    // insert dans table fraisforfait
    $query_fraisforfait = "INSERT INTO fraisforfait (fiche_id, montant) VALUES ('$fiche_id', '$montant')";
    mysqli_query($conn, $query_fraisforfait);

    // insert dans table lignefraisforfait
    $query_lignefraisforfait = "INSERT INTO lignefraisforfait (fiche_id, date, montant) VALUES ('$fiche_id', '$date', '$montant')";
    mysqli_query($conn, $query_lignefraisforfait);
} else {
    // si pas forfait, insert dans table hors forfait
    $query_lignefraishorsforfait = "INSERT INTO lignefraishorsforfait (fiche_id, date, montant, description) VALUES ('$fiche_id', '$date', '$montant', '$description')";
    mysqli_query($conn, $query_lignefraishorsforfait);
}

// message de succès affiché à l'utilisateur
echo "Frais enregistré avec succès !";

// fermeture de la connexion bdd
mysqli_close($conn);
?>

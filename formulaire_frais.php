<?php
// démarre session pour récup infos user
session_start();

// vérif que user connecté et a le rôle admin (id_role = 1), sinon redirige
if (!isset($_SESSION['id_role'], $_SESSION['id_user']) || $_SESSION['id_role'] != 1) {
    header("Location: login.php");
    exit();
}

// config bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // init connexion à la bdd via pdo + encodage utf8
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    
    // active les erreurs pdo en mode exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // stoppe le script si pb de connexion bdd
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// dossier où stocker les fichiers justificatifs
$cheminUpload = "uploads/";

// crée le dossier si existe pas
if (!is_dir($cheminUpload)) {
    mkdir($cheminUpload, 0777, true);
}

// vérif si formulaire envoyé (en POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // récup et sécurisation des montants
    $montants = [
        "hebergement" => !empty($_POST['montant_hebergement']) ? (float)$_POST['montant_hebergement'] : 0.00,
        "repas" => !empty($_POST['montant_repas']) ? (float)$_POST['montant_repas'] : 0.00,
        "deplacement" => !empty($_POST['montant_deplacement']) ? (float)$_POST['montant_deplacement'] : 0.00,
        "hors_frais" => !empty($_POST['montant_hors_frais']) ? (float)$_POST['montant_hors_frais'] : 0.00
    ];

    // récup et sécurisation des quantités (par défaut 1)
    $quantites = [
        "hebergement" => !empty($_POST['quantite_hebergement']) ? (int)$_POST['quantite_hebergement'] : 1,
        "repas" => !empty($_POST['quantite_repas']) ? (int)$_POST['quantite_repas'] : 1,
        "deplacement" => !empty($_POST['quantite_deplacement']) ? (int)$_POST['quantite_deplacement'] : 1,
        "hors_frais" => !empty($_POST['quantite_hors_frais']) ? (int)$_POST['quantite_hors_frais'] : 1
    ];

    // récup date et commentaire
    $date = !empty($_POST['date']) ? $_POST['date'] : null;
    $commentaire = !empty($_POST['commentaire']) ? htmlspecialchars(trim($_POST['commentaire'])) : null;

    // vérif que user existe bien en bdd
    $id_user = $_SESSION['id_user'];
    $requeteUser = $connexion->prepare("SELECT id_user FROM user WHERE id_user = :id_user");
    $requeteUser->bindParam(':id_user', $id_user);
    $requeteUser->execute();
    $utilisateur = $requeteUser->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        die("Utilisateur introuvable.");
    }

    // insertion de la fiche de frais
    $sqlFiche = "INSERT INTO fiche (id_user, date, commentaire) VALUES (:id_user, :date, :commentaire)";
    $stmtFiche = $connexion->prepare($sqlFiche);
    $stmtFiche->bindParam(':id_user', $id_user);
    $stmtFiche->bindParam(':date', $date);
    $stmtFiche->bindParam(':commentaire', $commentaire);

    try {
        // enregistre la fiche
        $stmtFiche->execute();

        // récup id de la fiche ajoutée
        $id_fiche = $connexion->lastInsertId();

        // mapping des types de frais avec id en bdd
        $typesFrais = [
            "hebergement" => 1,
            "repas" => 2,
            "deplacement" => 3,
            "hors_frais" => 5
        ];

        // boucle sur chaque type de frais
        foreach ($montants as $type => $montant) {
            if ($montant > 0) {
                // récup fichier justificatif s’il existe
                $justificatif = isset($_FILES['justificatif_' . $type]) ? $_FILES['justificatif_' . $type] : null;
                $nomJustificatif = null;

                // si fichier uploadé sans erreur
                if ($justificatif && $justificatif['error'] == 0) {
                    $nomJustificatif = $cheminUpload . basename($justificatif['name']);

                    // tentative de move du fichier vers le dossier
                    if (!move_uploaded_file($justificatif['tmp_name'], $nomJustificatif)) {
                        $nomJustificatif = null; // en cas d'échec
                    }
                }

                // insertion dans table ligne_frais
                $sqlLigneFrais = "INSERT INTO ligne_frais (id_fiche, id_typefrais, quantite, prix_unitaire, date_depense, justificatif) 
                                  VALUES (:id_fiche, :id_typefrais, :quantite, :prix_unitaire, :date_depense, :justificatif)";
                $stmtLigneFrais = $connexion->prepare($sqlLigneFrais);
                $stmtLigneFrais->bindParam(':id_fiche', $id_fiche);
                $stmtLigneFrais->bindParam(':id_typefrais', $typesFrais[$type]);
                $stmtLigneFrais->bindParam(':quantite', $quantites[$type]);
                $stmtLigneFrais->bindParam(':prix_unitaire', $montant);
                $stmtLigneFrais->bindParam(':date_depense', $date);
                $stmtLigneFrais->bindParam(':justificatif', $nomJustificatif);
                $stmtLigneFrais->execute();
            }
        }

        // message success en html
        $message = "<p style='color:green; text-align:center; font-weight:bold;'>Les frais ont été enregistrés avec succès !</p>";
        echo $message;

    } catch (PDOException $e) {
        // en cas d'erreur lors de l'insert
        $message = "Erreur lors de l'enregistrement des données : " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement des frais</title>
    <style>
        body {
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo img {
            height: 50px;
        }

        .navbar-buttons a {
            text-decoration: none;
            color: white;
            margin: 0 10px;
            padding: 10px 20px;
            border: 2px solid white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar-buttons a:hover {
            background-color: white;
            color: #007bff;
        }

        .navbar-user {
            color: white;
            font-size: 16px;
        }

        .content {
            text-align: center;
            padding: 20px;
        }
        .expense-row {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .expense-block {
            background-color: white;
            border-radius: 10px;
            padding: 10px;
            width: 23%;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="number"], input[type="date"], textarea, input[type="file"], input[type="submit"] {
            margin: 5px;
            padding: 10px;
            width: 80%;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
<nav>
<div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
 
    <div class="navbar-buttons">
        <a href="accueil_visiteur.php">Accueil</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
    <div>Connecté en tant que : <?php echo $_SESSION['role']; ?></div>
</nav>
<div class="container">
    <h1>Enregistrement des frais</h1>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="expense-row">
            <?php $types = ['hebergement' => 'Hébergement', 'repas' => 'Repas', 'deplacement' => 'Déplacement', 'hors_frais' => 'Hors Frais', 'train' => 'Train']; ?>
            <?php foreach ($types as $key => $label): ?>
                <div class="expense-block">
                    <h2><?php echo $label; ?></h2>
                    <label>Montant :
                        <input type="number" name="montant_<?php echo $key; ?>" value="0" required>
                    </label>
                    <label>Quantité :
                        <input type="number" name="quantite_<?php echo $key; ?>" min="1" value="1" required>
                    </label>
                    <label>Justificatif :
                        <input type="file" name="justificatif_<?php echo $key; ?>">
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <h2>Commentaire</h2>
        <textarea name="commentaire" rows="4" placeholder="Ajoutez un commentaire..."></textarea>
        <h2>Date</h2>
        <input type="date" name="date" required>
        <input type="submit" value="Enregistrer">
    </form>
</div>
</body>
</html>

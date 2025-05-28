<?php
// démarre la session
session_start();

// vérif que l'utilisateur est connecté et a le rôle visiteur (id_role = 1), sinon redirige
if (!isset($_SESSION['id_role'], $_SESSION['id_user']) || $_SESSION['id_role'] != 1) {
    header("Location: login.php");
    exit();
}

// config connexion à la bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // init de la connexion avec pdo + encodage utf8
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // affiche erreur si pb de connexion
    die("Erreur : " . $e->getMessage());
}

// vérif que l'id de la fiche est bien présent en GET
if (!isset($_GET['id_fiche'])) {
    die("ID de fiche manquant.");
}

// sécurise l'id de fiche en int
$id_fiche = intval($_GET['id_fiche']);

// récup tous les types de frais (pour affichage dans le form plus bas)
$stmt = $connexion->prepare("SELECT * FROM type_frais");
$stmt->execute();
$types_frais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// si formulaire envoyé (POST), on traite les données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentaire = $_POST['commentaire'];
    $date = $_POST['date'];
    $id_types = $_POST['id_type'];
    $quantites = $_POST['quantite'];
    $prix_unitaires = $_POST['prix_unitaire'];

    // maj de la fiche : commentaire et date
    $stmt = $connexion->prepare("UPDATE fiche SET commentaire = :commentaire, date = :date WHERE id_fiche = :id_fiche AND id_user = :id_user");
    $stmt->execute([
        ':commentaire' => $commentaire,
        ':date' => $date,
        ':id_fiche' => $id_fiche,
        ':id_user' => $_SESSION['id_user']
    ]);

    // supprime les anciennes lignes de frais pour cette fiche
    $stmt = $connexion->prepare("DELETE FROM ligne_frais WHERE id_fiche = :id_fiche");
    $stmt->execute([':id_fiche' => $id_fiche]);

    // réinsère chaque ligne de frais saisie
    for ($i = 0; $i < count($id_types); $i++) {
        if (!empty($id_types[$i]) && is_numeric($quantites[$i]) && is_numeric($prix_unitaires[$i])) {
            $stmt = $connexion->prepare("INSERT INTO ligne_frais (id_fiche, id_typefrais, quantite, prix_unitaire) VALUES (:id_fiche, :id_typefrais, :quantite, :prix_unitaire)");
            $stmt->execute([
                ':id_fiche' => $id_fiche,
                ':id_typefrais' => $id_types[$i],
                ':quantite' => $quantites[$i],
                ':prix_unitaire' => $prix_unitaires[$i]
            ]);
        }
    }

    // redirection après maj terminée
    header("Location: fiche_frais_visiteur.php");
    exit();
}

// récup données de la fiche ciblée
$stmt = $connexion->prepare("SELECT * FROM fiche WHERE id_fiche = :id_fiche AND id_user = :id_user");
$stmt->execute([':id_fiche' => $id_fiche, ':id_user' => $_SESSION['id_user']]);
$fiche = $stmt->fetch(PDO::FETCH_ASSOC);

// si fiche non trouvée, stoppe avec msg d'erreur
if (!$fiche) {
    die("Fiche introuvable.");
}

// récup toutes les lignes de frais rattachées à la fiche
$stmt = $connexion->prepare("SELECT * FROM ligne_frais WHERE id_fiche = :id_fiche");
$stmt->execute([':id_fiche' => $id_fiche]);
$lignes_frais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la fiche de frais</title>
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-logo img {
            height: 50px;
        }

        .navbar-buttons a {
            text-decoration: none;
            color: white;
            margin: 0 10px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: transparent;
            border: 2px solid white;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .navbar-buttons a:hover {
            background-color: white;
            color: #007bff;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label, input, select, textarea {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }

        input[type="text"], input[type="number"], input[type="date"], select, textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .ligne-frais {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #eef2f9;
            border-radius: 8px;
        }

        button {
            padding: 10px 20px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    
<div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
        <div class="navbar-buttons">
            <a href="accueil_visiteur.php">Accueil</a>
            <a href="deconnexion.php">Déconnexion</a>
        </div>
        <div class="navbar-user">
            Connecté en tant que : <?php echo $_SESSION['role']; ?>
        </div>
    </div>


<h2>Modifier la fiche de frais</h2>

<form method="post">
    <label for="date">Date :</label>
    <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($fiche['date']); ?>" required>

    <label for="commentaire">Commentaire :</label>
    <textarea name="commentaire" id="commentaire" rows="4"><?php echo htmlspecialchars($fiche['commentaire']); ?></textarea>

    <h3>Lignes de frais :</h3>

    <div id="frais-container">
        <?php foreach ($lignes_frais as $index => $ligne): ?>
            <div class="ligne-frais">
                <label for="type-<?php echo $index; ?>">Type de frais :</label>
                <select name="id_type[]" required>
    <option value="">-- Sélectionner un type --</option>
    <?php foreach ($types_frais as $type): ?>
        <option value="<?= $type['id_lf']; ?>" <?= $type['id_lf'] == $ligne['id_typefrais'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($type['type']); ?>
        </option>
    <?php endforeach; ?>
</select>

                <label>Quantité :</label>
                <input type="number" name="quantite[]" value="<?php echo (int)$ligne['quantite']; ?>" required>

                <label>Prix unitaire (€) :</label>
                <input type="number" name="prix_unitaire[]" step="0.01" value="<?php echo number_format($ligne['prix_unitaire'], 2, '.', ''); ?>" required>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit">Enregistrer les modifications</button>
</form>

</body>
</html>

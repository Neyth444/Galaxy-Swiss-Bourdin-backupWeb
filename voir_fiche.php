<?php
// démarre session
session_start();

// vérif que session active, sinon redirige vers login
if (!isset($_SESSION['id_role'])) {
    header("Location: login.php");
    exit();
}

// config connexion bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // connexion à la bdd avec pdo + gestion des erreurs activée
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // vérif que l'id de la fiche est bien présent en GET
    if (isset($_GET['id_fiche'])) {
        $id_fiche = intval($_GET['id_fiche']);

        // récup status de la fiche (ex: Non Traité, Traité, etc.)
        $stmt_etat = $connexion->prepare("SELECT status FROM fiche WHERE id_fiche = :id_fiche");
        $stmt_etat->bindParam(':id_fiche', $id_fiche);
        $stmt_etat->execute();
        $etat_fiche = $stmt_etat->fetch(PDO::FETCH_ASSOC)['status'];

        // récup détails des lignes de frais (type, quantité, prix, date, justificatif)
        $stmt = $connexion->prepare("
            SELECT l.id_typefrais, l.quantite, l.prix_unitaire, l.date_depense, l.justificatif, t.type
            FROM ligne_frais l
            JOIN type_frais t ON l.id_typefrais = t.id_lf
            WHERE l.id_fiche = :id_fiche
        ");
        $stmt->bindParam(':id_fiche', $id_fiche);
        $stmt->execute();
        $details_fiche = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // calcule total des frais de la fiche (somme des quantités × prix unitaires)
        $stmt_total = $connexion->prepare("
            SELECT SUM(l.quantite * l.prix_unitaire) AS total_frais
            FROM ligne_frais l
            WHERE l.id_fiche = :id_fiche
        ");
        $stmt_total->bindParam(':id_fiche', $id_fiche);
        $stmt_total->execute();
        $total_frais = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_frais'];

    } else {
        // si aucun id_fiche fourni, stoppe avec msg
        die("Aucune fiche sélectionnée.");
    }

} catch (PDOException $e) {
    // affiche msg si erreur lors de la connexion ou requête
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la fiche</title>
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

        .navbar-logo {  
            display: flex;
            justify-content: flex-end;
            flex-direction: row-reverse;
            align-items: center;
            flex-grow: 2;
        }

        .navbar-logo img {
            height: 50px;
        }

        .navbar-buttons {
            flex-grow: 2;
            text-align: center;
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

        .navbar-user {
            flex-grow: 1;
            text-align: center;
            color: white;
            font-size: 16px;
        }
        .content {
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
        .etat {
            font-size: 1.2em;
            font-weight: bold;
            margin: 10px 0;
        }
        .etat.valid {
            color: green;
        }
        .etat.refused {
            color: red;
        }
        .etat.pending {
            color: orange;
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
            Connecté en tant que : <?php echo $_SESSION['role'] ; ?>
        </div>
    </div>
    <div class="content">
        <h1>Détails de la fiche</h1>

        <p class="etat <?php echo strtolower($etat_fiche); ?>">
            Etat de la fiche : <?php echo htmlspecialchars($etat_fiche); ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Type de frais</th>
                    <th>Quantité</th>
                    <th>Prix unitaire (€)</th>
                    <th>Total (€)</th>
                    <th>Date de dépense</th>
                    <th>Justificatif</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details_fiche as $detail): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detail['type']); ?></td>
                        <td><?php echo htmlspecialchars($detail['quantite']); ?></td>
                        <td><?php echo number_format($detail['prix_unitaire'], 2, ',', ' '); ?> €</td>
                        <td><?php echo number_format($detail['quantite'] * $detail['prix_unitaire'], 2, ',', ' '); ?> €</td>
                        <td><?php echo htmlspecialchars($detail['date_depense']); ?></td>
                        <td>
                            <?php if (!empty($detail['justificatif'])): ?>
                                <a href="uploads/<?php echo htmlspecialchars($detail['justificatif']); ?>" target="_blank">Voir justificatif</a>
                            <?php else: ?>
                                Aucun justificatif
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total">Total des frais : <?php echo number_format($total_frais, 2, ',', ' '); ?> €</p>
    </div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 2) {
    header("Location: login.php");
    exit();
}

$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les frais traités
    $requete = $connexion->prepare("
        SELECT u.fname, u.lname, f.id_fiche 
        FROM fiche f
        JOIN user u ON f.id_user = u.id_user
        WHERE f.status = 'Traité'
    ");
    $requete->execute();
    $frais_traite = $requete->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frais Traités</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f0f8ff;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
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
        .button-view {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .button-view:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="accueil_comptable.php">Accueil</a>
        <a href="frais_NT.php">Frais Non-Traités</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
    <div class="content">
        <h1>Frais Utilisateurs Traités</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Numéro Fiche</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($frais_traite as $frais): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($frais['lname']); ?></td>
                        <td><?php echo htmlspecialchars($frais['fname']); ?></td>
                        <td><?php echo htmlspecialchars($frais['id_fiche']); ?></td>
                        <td>
                            <a href="voir_fiche.php?id_fiche=<?php echo $frais['id_fiche']; ?>" class="button-view">Voir la fiche</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

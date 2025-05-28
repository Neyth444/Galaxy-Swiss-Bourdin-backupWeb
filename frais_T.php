<?php
// démarre la session
session_start();

// vérif que user connecté a le rôle comptable (id_role = 2), sinon redirige vers login
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 2) {
    header("Location: login.php");
    exit();
}

// config connexion bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // connexion à la bdd via pdo + encodage utf8
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // récup des fiches ayant le statut "Traité"
    $requete = $connexion->prepare("
        SELECT u.fname, u.lname, f.id_fiche, f.etat
        FROM fiche f
        JOIN user u ON f.id_user = u.id_user
        WHERE f.status = 'Traité'
    ");

    // exécution de la requête
    $requete->execute();

    // stockage des résultats dans un tableau assoc
    $frais_traite = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // si erreur de connexion bdd, affiche message et stoppe script
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
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
        <div class="navbar-buttons">
        <a href="accueil_comptable.php">Accueil</a>
        <a href="frais_NT.php">Frais Non-Traités</a>
        <a href="deconnexion.php">Déconnexion</a>
        </div>
        <div class="navbar-user">
            Connecté en tant que : <?php echo $_SESSION['role']; ?>
        </div>
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
                    <th>Statut</th>
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
                        <td><?php echo htmlspecialchars($frais['etat']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// démarre la session
session_start();

// vérif que user connecté a bien le rôle comptable (id_role = 2), sinon redirige vers login
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 2) {
    header("Location: login.php");
    exit();
}

// config de connexion bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // connexion à la bdd via pdo + encodage utf8
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // si formulaire envoyé en POST, on traite les données
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // vérif que les champs nécessaires sont présents
        if (isset($_POST['action'], $_POST['id_fiche'])) {
            $action = $_POST['action']; // "valider" ou "refuser"
            $id_fiche = intval($_POST['id_fiche']);
            $commentaire = !empty($_POST['commentaireComptable']) ? htmlspecialchars(trim($_POST['commentaireComptable'])) : null;

            // par défaut on passe le status à "Traité", état reste "En attente"
            $new_status = 'Traité';
            $new_etat = 'En attente';

            // maj de l'état selon l'action choisie
            if ($action === 'valider') {
                $new_etat = 'Validée';
            } elseif ($action === 'refuser') {
                $new_etat = 'Refusée';
            }

            // requête pour mettre à jour la fiche
            $stmt = $connexion->prepare("
                UPDATE fiche 
                SET status = :status, etat = :etat, commentaireComptable = :commentaireComptable 
                WHERE id_fiche = :id_fiche
            ");
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':etat', $new_etat);
            $stmt->bindParam(':commentaireComptable', $commentaire);
            $stmt->bindParam(':id_fiche', $id_fiche);
            $stmt->execute();
        }
    }

    // récupère les fiches avec status "Non Traité"
    $requete = $connexion->prepare("
        SELECT u.fname, u.lname, f.id_fiche, f.commentaire 
        FROM fiche f
        JOIN user u ON f.id_user = u.id_user
        WHERE f.status = 'Non Traité'
    ");
    $requete->execute();
    $frais_non_traite = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // stoppe le script si connexion bdd échoue
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frais Non Traités</title>
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
        .button-view, .button-validate, .button-reject {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .button-view {
            background-color: #28a745;
        }
        .button-validate {
            background-color: #007bff;
        }
        .button-reject {
            background-color: #dc3545;
        }
        .button-view:hover {
            background-color: #218838;
        }
        .button-validate:hover {
            background-color: #0056b3;
        }
        .button-reject:hover {
            background-color: #c82333;
        }
        textarea {
            width: 100%;
            height: 60px;
            margin: 10px 0;
            resize: none;
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
        <a href="frais_T.php">Frais Traités</a>
        <a href="deconnexion.php">Déconnexion</a>
        </div>
        <div class="navbar-user">
            Connecté en tant que : <?php echo $_SESSION['role']; ?>
        </div>
    </div>

    <div class="content">
        <h1>Frais Utilisateurs Non-Traités</h1>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>ID Fiche</th>
                    <th>Commentaire</th>
                    <th>Action</th>
                    <th>Validation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($frais_non_traite as $frais): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($frais['lname']); ?></td>
                        <td><?php echo htmlspecialchars($frais['fname']); ?></td>
                        <td><?php echo htmlspecialchars($frais['id_fiche']); ?></td>
                        <td><?php echo htmlspecialchars($frais['commentaire']); ?></td>
                        <td>
                            <a href="voir_fiche.php?id_fiche=<?php echo $frais['id_fiche']; ?>" class="button-view">Voir la fiche</a>
                        </td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_fiche" value="<?php echo $frais['id_fiche']; ?>">
                                <textarea name="commentaireComptable" placeholder="Ajoutez un commentaire"></textarea>
                                <button type="submit" name="action" value="valider" class="button-validate">Valider</button>
                                <button type="submit" name="action" value="refuser" class="button-reject">Refuser</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

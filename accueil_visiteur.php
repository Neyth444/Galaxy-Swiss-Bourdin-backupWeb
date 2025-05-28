<?php
session_start();
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
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
            padding: 20px;
            text-align: center;
        }

        .block-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .block {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            width: 300px;
            text-align: center;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .block h2 {
            margin-bottom: 10px;
            color: #007bff;
        }

        .spacer {
  height: 150px;
}
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
        <div class="navbar-buttons">
            <a href="fiche_frais_visiteur.php">Mes fiches frais</a>
            <a href="formulaire_frais.php">Nouveau frais</a>
            <a href="deconnexion.php">Déconnexion</a>
            <a href="user_profil.php">Mon profile</a>
        </div>
        <div class="navbar-user">
            Connecté en tant que : <?php echo $_SESSION['role'] ; ?>
        </div>
    </div>

    <div class="content">
        <h1>Bienvenue sur l'application GSB</h1>
        <div class="spacer"></div>
      

        <div class="block-container">
            <div class="block">
                <h2>GSB Newsletter</h2>
                <p>Inscrivez-vous à notre newsletter pour recevoir les dernières actualités.</p>
            </div>
            <div class="block">
                <h2>Nos derniers produits</h2>
                <p>Découvrez nos derniers produits et services.</p>
            </div>
            <div class="block"> 
                <h2>Fiche Frais</h2>
                <p>Consultez et gérez vos fiches de frais.</p>
                <a href="fiche_frais_visiteur.php">Accéder à mes fiches de frais</a>
            </div>
        </div>
    </div>
</body>
</html>
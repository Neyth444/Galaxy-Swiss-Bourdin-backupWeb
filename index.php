<?php
session_start();

// Déconnexion automatique à chaque visite sur l'index pour s'assurer que c'est une page neutre
session_unset();
session_destroy();

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
        justify-content: center;
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
            gap: 20px;
        }

        .block {
            border: 1px solid #ccc;
            padding: 20px;
            width: 250px;
            text-align: center;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .block h2 {
            margin-bottom: 10px;
            color: #007bff;
        }

        .block a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .block a:hover {
            background-color: #0056b3;
        }

        .block:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
    </div>

    <div class="content">
        <h1>Identifiez vous</h1>

        <div class="block-container">
            <div class="block">
                <h2>Visiteur</h2>
                <p>Accédez aux fonctionnalités dédiées aux visiteurs.</p>
                <a href="accueil_visiteur.php">Accéder</a>
            </div>
            <div class="block">
                <h2>Comptable</h2>
                <p>Gérez les fiches de frais et autres tâches comptables.</p>
                <a href="accueil_comptable.php">Accéder</a>
            </div>
            <div class="block">
                <h2>Administrateur</h2>
                <p>Administrez l'application et gérez les utilisateurs.</p>
                <a href="accueil_admin.php">Accéder</a>
            </div>
        </div>
    </div>
</body>
</html>

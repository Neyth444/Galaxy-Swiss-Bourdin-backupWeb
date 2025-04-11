<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de connexion</title>
    <style>
        body {
            background-color: #f0f8ff; 
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column; /* Pour empiler navbar et contenu */
            align-items: center;
            height: 100vh;
        }

        .navbar {
            width: 100%;
            background-color: #007bff; 
            color: white;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-logo {  
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .navbar-logo img {
            height: 50px;
        }

        .form-container {
            background-color: white;
            width: 300px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 50px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        img {
            width: 70px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
        }

        button:hover {
            background-color: #005cbf;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
    </div>

    <div class="form-container">
        <h2>Connexion</h2>
        <form action="process_form_login.php" method="POST">
            <label for="login">Login :</label>
            <input type="text" id="login" name="login" />

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password"  />

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>

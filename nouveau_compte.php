<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        .form-container {
            background-color: #ffffff;
            width: 300px;
            margin: 100px auto;
            padding: 3%;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 50%;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: lightgrey;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <img src="img/gsb.png" alt="Logo">
        <h2>Création d'un nouvel utilisateur</h2>
        <form action="process_form_signup.php" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirmation du mot de passe :</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="">-- Sélectionnez un rôle --</option>
                <option value="1">Visiteur</option>
                <option value="2">Comptable</option>
                <option value="3">Administrateur</option>
            </select>

            <button type="submit">Envoyer</button>
        </form>
    </div>

</body>
</html>

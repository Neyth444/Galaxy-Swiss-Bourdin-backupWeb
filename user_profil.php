<?php
// démarre session
session_start();

// vérif que le user est connecté et a un rôle valide (1, 2 ou 3), sinon redirige
if (!isset($_SESSION['id_role'], $_SESSION['id_user']) || !in_array($_SESSION['id_role'], [1, 2, 3])) {
    header("Location: login.php");
    exit();
}

// détermine la page d'accueil selon le rôle
$homePage = '';
if (isset($_SESSION['id_role'])) {
    switch ($_SESSION['id_role']) {
        case 1:
            $homePage = 'accueil_visiteur.php';
            break;
        case 2:
            $homePage = 'accueil_comptable.php';
            break;
        case 3:
            $homePage = 'accueil_admin.php';
            break;
        default:
            $homePage = 'login.php';
    }
}

// config bdd
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bisounours";

// message à afficher après action
$message = '';

try {
    // connexion pdo + gestion des erreurs activée
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // si formulaire soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $email = trim($_POST['email']);

        // vérif que tous les champs sont remplis
        if (!empty($fname) && !empty($lname) && !empty($email)) {
            // maj des infos user en bdd
            $stmt = $pdo->prepare("
                UPDATE user 
                SET fname = :fname, lname = :lname, email = :email 
                WHERE id_user = :id_user
            ");
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_user', $_SESSION['id_user']);
            $stmt->execute();

            // msg de confirmation
            $message = "Modification appliquée avec succès !";
        } else {
            // msg si champs manquants
            $message = "Veuillez remplir tous les champs.";
        }
    }

    // récup infos actuelles de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT fname, lname, email FROM user WHERE id_user = :id_user");
    $stmt->bindParam(':id_user', $_SESSION['id_user']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // si aucun user trouvé => stop avec msg
    if ($user === false) {
        die("Erreur : Aucun utilisateur trouvé pour l'identifiant donné.");
    }

} catch (PDOException $e) {
    // msg d'erreur si exception pdo
    die("Erreur : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil</title>
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

        .form-container {
            width: 50%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .message {
            color: green;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="img/gsb.png" alt="Logo GSB">
        </div>
        <div class="navbar-buttons">
        <a href="<?php echo htmlspecialchars($homePage); ?>">Accueil</a>
        <a href="deconnexion.php">Déconnexion</a>
        </div>
    </div>

    <div class="content">
        <h1>Modifier le profil</h1>

        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form class="form-container" action="" method="POST">
            <label for="fname">Prénom :</label>
            <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>

            <label for="lname">Nom :</label>
            <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>

            <button type="submit">Appliquer les modifications</button>
        </form>
    </div>
</body>
</html>

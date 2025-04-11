<?php
session_start();
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 3) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bisounours";

$message = ''; // Variable pour message 

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supprimer un utilisateur
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
        $id_user_to_delete = intval($_POST['delete_user']);
        $stmt = $pdo->prepare("DELETE FROM user WHERE id_user = :id_user");
        $stmt->bindParam(':id_user', $id_user_to_delete);
        $stmt->execute();
        $message = "L'utilisateur a bien été supprimé.";
    }

    // Mettre à jour un utilisateur
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
        $id_user = intval($_POST['id_user']);
        $prenom = htmlspecialchars(trim($_POST['fname']));
        $nom = htmlspecialchars(trim($_POST['lname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $role = intval($_POST['role']);

        $stmt = $pdo->prepare("UPDATE user SET fname = :prenom, lname = :nom, email = :email, role = :role WHERE id_user = :id_user");
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->execute();

        $message = "Les informations de l'utilisateur ont été mises à jour.";
    }

    // Récupération des données des utilisateurs
    $users = $pdo->query("
        SELECT u.id_user, u.fname, u.lname, u.email, r.role AS role_name, r.id_role 
        FROM user u 
        JOIN role r ON u.role = r.id_role
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des rôles disponibles
    $roles = $pdo->query("SELECT id_role, role FROM role")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les utilisateurs</title>
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

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .button-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .button-delete:hover {
            background-color: #c82333;
        }

        .button-update {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .button-update:hover {
            background-color: #218838;
        }

        .message {
            color: green;
            font-weight: bold;
            margin: 20px auto;
        }

        .button-new-account {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
            transition: background-color 0.3s;
        }

        .button-new-account:hover {
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
            <a href="nouveau_compte.php">Nouvel utilisateur</a>
            <a href="accueil_admin.php">Accueil</a>
            <a href="deconnexion.php">Déconnexion</a>
        </div>
        <div class="navbar-user">
            Connecté en tant que : <?php echo htmlspecialchars($_SESSION['role']); ?>
        </div>
    </div>

    <div class="content">
        <h1>Gérer les utilisateurs</h1>

        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <form method="post">
                            <td><?php echo $user['id_user']; ?></td>
                            <td><input type="text" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required></td>
                            <td><input type="text" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
                            <td>
                                <select name="role" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['id_role']; ?>" <?php echo ($role['id_role'] == $user['id_role']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['role']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>">
                                <button type="submit" name="update_user" class="button-update">Modifier</button>
                                <button type="submit" name="delete_user" value="<?php echo $user['id_user']; ?>" class="button-delete">Supprimer</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

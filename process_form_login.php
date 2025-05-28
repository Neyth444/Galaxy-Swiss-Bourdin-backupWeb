<?php
// démarre session pour stocker les infos user
session_start();

// config de connexion à la bdd
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // init connexion pdo avec encodage utf8
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // stoppe le script si la connexion échoue
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// vérif que la requête est bien en POST (form soumis)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    // redirige si un des champs est vide
    if (empty($login) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    // récup l'utilisateur via son mail (champ login)
    $requete = $connexion->prepare("
        SELECT u.*, r.id_role, r.role 
        FROM user u
        JOIN role r ON u.role = r.id_role
        WHERE u.email = :login
    ");
    $requete->bindParam(':login', $login);
    $requete->execute();

    // récup des infos user
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    // si user trouvé et mdp ok, alors login réussi
    if ($utilisateur && password_verify($password, $utilisateur['password'])) {
        // enregistre les infos user en session
        $_SESSION['id_user'] = $utilisateur['id_user']; 
        $_SESSION['fname'] = $utilisateur['fname'];
        $_SESSION['lname'] = $utilisateur['lname'];
        $_SESSION['id_role'] = $utilisateur['id_role'];
        $_SESSION['role'] = $utilisateur['role']; 

        // redirige vers la page d'accueil selon le rôle
        switch ($utilisateur['id_role']) {
            case 1:
                header("Location: accueil_visiteur.php");
                break;
            case 2:
                header("Location: accueil_comptable.php");
                break;
            case 3:
                header("Location: accueil_admin.php");
                break;
            default:
                header("Location: login.php?error=invalid_role");
        }
        exit();
    } else {
        // si login fail, redirige avec erreur
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    // si accédé sans POST, renvoie vers page login
    header("Location: login.php");
    exit();
}
?>

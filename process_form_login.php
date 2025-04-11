
<?php
session_start();

$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    // Vérifie que les champs ne sont pas vides
    if (empty($login) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    // Récupération de l'utilisateur par son email
    $requete = $connexion->prepare("
        SELECT u.*, r.id_role, r.role 
        FROM user u
        JOIN role r ON u.role = r.id_role
        WHERE u.email = :login
    ");
    $requete->bindParam(':login', $login);
    $requete->execute();

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    // check si l'utilisateur existe et si le mot de passe est correct
    if ($utilisateur && password_verify($password, $utilisateur['password'])) {
        $_SESSION['id_user'] = $utilisateur['id_user']; 
        $_SESSION['fname'] = $utilisateur['fname'];
        $_SESSION['lname'] = $utilisateur['lname'];
        $_SESSION['id_role'] = $utilisateur['id_role'];
        $_SESSION['role'] = $utilisateur['role']; 
        switch ($utilisateur['id_role']) {
            case 1: // Visiteur
                header("Location: accueil_visiteur.php");
                break;
            case 2: // Comptable
                header("Location: accueil_comptable.php");
                break;
            case 3: // Administrateur
                header("Location: accueil_admin.php");
                break;
            default:
                header("Location: login.php?error=invalid_role");
        }
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

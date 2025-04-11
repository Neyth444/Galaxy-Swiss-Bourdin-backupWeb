<?php
// Démarrer la session
session_start();

// Informations de connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = ""; // Pas de mot de passe
$nomBDD = "bisounours";

try {
    // Connexion à la base de données via PDO
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier que le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération et validation des données du formulaire
    $nom = htmlspecialchars(trim($_POST["nom"]));
    $prenom = htmlspecialchars(trim($_POST["prenom"]));
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirmPassword) {
        die("Les mots de passe ne correspondent pas.");
    }

    // Vérifier si l'email est valide
    if (!$email) {
        die("Adresse email invalide.");
    }

    // Hacher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si l'utilisateur existe déjà
    $requeteExist = $connexion->prepare("SELECT * FROM User WHERE email = :email");
    $requeteExist->bindParam(':email', $email);
    $requeteExist->execute();

    if ($requeteExist->rowCount() > 0) {
        die("Un compte avec cet email existe déjà.");
    }

    // Insérer l'utilisateur dans la table User
    $role = 1; // Rôle par défaut : Visiteur
    $requete = $connexion->prepare("INSERT INTO User (fname, lname, email, password, role) 
                                    VALUES (:prenom, :nom, :email, :password, :role)");
    $requete->bindParam(':prenom', $prenom);
    $requete->bindParam(':nom', $nom);
    $requete->bindParam(':email', $email);
    $requete->bindParam(':password', $hashedPassword);
    $requete->bindParam(':role', $role);

    if ($requete->execute()) {
        echo "Inscription réussie ! <a href='index.php'>Connectez-vous ici</a>";
    } else {
        echo "Erreur lors de l'inscription.";
    }
}



            // source : 
            //www.php.net/manual/fr/pdostatement.bindparam.php
            //www.php.net/manual/fr/pdo.prepare.php
            //www.pierre-giraud.com/php-mysql-apprendre-coder-cours/requete-preparee/
            //www.php.net/manual/fr/book.pdo.php
            // et mon ancien code 
?>

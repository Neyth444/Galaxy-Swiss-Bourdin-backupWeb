<?php
// info bdd
$serveur = "localhost";      // Serveur MySQL
$utilisateur = "root";       // Nom d'utilisateur MySQL
$mdpBDD = "";         // Mot de passe MySQL (ici, il est vide)
$nomBDD = "bisounours";      // Nom de la base de données

try {
    // connexion a la bdd via le PDO et non via mysqli 
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    // configuration du pdo pour afficher les erreurs
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // si on ne peut pas se connecter a la bdd, alors le message suivant s'affiche
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // on recup les données du formulaire html
    $nomUtilisateur = $_POST["pseudo"];       // conf du pseudo
    $emailUtilisateur = $_POST["email"];      // conf de l'email
    $mdp = $_POST["password"];         // conf du mot de pass
    $confirmerMotDePasse = $_POST["confirm-password"];  // confirm du mdp

    // Vérifier si les deux mots de passe sont identiques
    if ($mdp === $confirmerMotDePasse) {
       
        $mdpCrypte = password_hash($mdp, PASSWORD_DEFAULT);

        $requeteSQL = $connexion->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe, date_inscription) VALUES (:pseudo, :email, :mot_de_passe, NOW())");

        $requeteSQL->bindParam(':pseudo', $nomUtilisateur);
        $requeteSQL->bindParam(':email', $emailUtilisateur);
        $requeteSQL->bindParam(':mot_de_passe', $mdpCrypte);

        // une fois toutes les infos rentrés, soit l'inscription se passe bien et 'inscription réussie" soit non et alors le message d'erreur s'affiche
        if ($requeteSQL->execute()) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    } else {
        echo "Les mots de passe ne sont pas identiques. Veuillez réessayer.";
    }
}



            // source : 
            //www.php.net/manual/fr/pdostatement.bindparam.php
            //www.php.net/manual/fr/pdo.prepare.php
            //www.pierre-giraud.com/php-mysql-apprendre-coder-cours/requete-preparee/
            //www.php.net/manual/fr/book.pdo.php
            // et mon ancien code 
?>

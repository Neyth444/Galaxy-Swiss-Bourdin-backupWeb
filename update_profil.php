<?php
session_start();
if (!isset($_SESSION['id_role'], $_SESSION['id_user']) || $_SESSION['id_role'] != 1) {
    header("Location: login.php");
    exit();
}

// Vérification des données du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);

    if (empty($fname) || empty($lname) || empty($email)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    // connexion bdd
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bisounours";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // preparation requete sql pour mettre a jour les infos de l'user
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

        // redirection pour eviter de tomber sur une page erreur
        header("Location: profil.php?success=1");
        exit();

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    header("Location: profil.php");
    exit();
}

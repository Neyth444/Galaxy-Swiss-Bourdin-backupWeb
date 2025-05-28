<?php
// démarre session pour vérif du user
session_start();

// accès réservé au rôle visiteur (id_role = 1)
if (!isset($_SESSION['id_role'], $_SESSION['id_user']) || $_SESSION['id_role'] != 1) {
    header("Location: login.php");
    exit();
}

// vérif si formulaire soumis en POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // récup et nettoie les champs
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);

    // si un champ est vide, stoppe avec message erreur
    if (empty($fname) || empty($lname) || empty($email)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    // config connexion bdd
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bisounours";

    try {
        // connexion pdo + gestion des erreurs activée
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // requête pour maj infos user
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

        // redirige vers page profil avec message de succès
        header("Location: profil.php?success=1");
        exit();

    } catch (PDOException $e) {
        // affiche message si erreur pdo
        die("Erreur : " . $e->getMessage());
    }
} else {
    // si accédé sans passer par POST, redirige vers le profil
    header("Location: profil.php");
    exit();
}

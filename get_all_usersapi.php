<?php
header("Content-Type: application/json");
include("connexion.php");

$sql = "
    SELECT u.id_user, u.fname, u.lname, u.email, u.role, r.role AS role_label
    FROM user u
    JOIN role r ON u.role = r.id_role
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "users" => $users
]);
?>

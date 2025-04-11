<?php
session_start();
if (!isset($_SESSION['id_role']) || $_SESSION['id_role'] != 3) {
    header("Location: login.php");
    exit();
}

$serveur = "localhost";
$utilisateur = "root";
$mdpBDD = "";
$nomBDD = "bisounours";

try {
    // Connexion à la base de données
    $connexion = new PDO("mysql:host=$serveur;dbname=$nomBDD;charset=utf8", $utilisateur, $mdpBDD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Moyenne des dépenses par type de frais
    $stmtMoyenne = $connexion->query("
        SELECT t.type, AVG(l.prix_unitaire * l.quantite) AS moyenne
        FROM ligne_frais l
        JOIN type_frais t ON l.id_typefrais = t.id_lf
        GROUP BY t.type
    ");
    $moyennes = $stmtMoyenne->fetchAll(PDO::FETCH_ASSOC);

    // Total des dépenses par type de frais
    $stmtTotal = $connexion->query("
        SELECT t.type, SUM(l.prix_unitaire * l.quantite) AS total
        FROM ligne_frais l
        JOIN type_frais t ON l.id_typefrais = t.id_lf
        GROUP BY t.type
    ");
    $totals = $stmtTotal->fetchAll(PDO::FETCH_ASSOC);

    $stmtUsers = $connexion->query("
    SELECT r.role AS nom_role, COUNT(u.id_user) AS total_users
    FROM user u
    JOIN role r ON u.role = r.id_role
    GROUP BY r.role
");

    $userStats = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 60%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table td {
            background-color: #fff;
        }

        canvas {
            margin: 20px auto;
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
    border: 2px solid white;
    transition: background-color 0.3s;
}

.navbar-buttons a:hover {
    background-color: white;
    color: #007bff;
}

.navbar-user {
    color: white;
    font-size: 16px;
}

    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar-logo">
        <img src="img/gsb.png" alt="Logo GSB">
    </div>
    <div class="navbar-buttons">
        <a href="accueil_admin.php">Accueil</a>
        <a href="manager_user.php">Gérer les utilisateurs</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
    <div class="navbar-user">
        Connecté en tant que : <?php echo $_SESSION['role']; ?>
    </div>
</div>


    <div class="content">
        <h2>Moyenne des Dépenses</h2>
        <canvas id="moyenneChart" width="400" height="200"></canvas>

        <h2>Total des Dépenses</h2>
        <canvas id="totalChart" width="400" height="200"></canvas>

        <h2>Nombre d'Utilisateurs par Type</h2>
        <canvas id="userChart" width="400" height="200"></canvas>

        <h2>Détails des Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Type d'Utilisateur</th>
                    <th>Nombre Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userStats as $userStat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($userStat['nom_role']); ?></td>
                        <td><?php echo htmlspecialchars($userStat['total_users']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Données récupérées depuis PHP
        const moyenneData = <?php echo json_encode($moyennes); ?>;
        const totalData = <?php echo json_encode($totals); ?>;
        const userData = <?php echo json_encode($userStats); ?>;

        // Préparation des données pour les graphiques
        const labelsMoyenne = moyenneData.map(item => item.type);
        const valuesMoyenne = moyenneData.map(item => parseFloat(item.moyenne));

        const labelsTotal = totalData.map(item => item.type);
        const valuesTotal = totalData.map(item => parseFloat(item.total));

        const labelsUsers = userData.map(item => item.nom_role);
        const valuesUsers = userData.map(item => parseInt(item.total_users));

        // Graphique des moyennes
        new Chart(document.getElementById('moyenneChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsMoyenne,
                datasets: [{
                    label: 'Moyenne des Dépenses (€)',
                    data: valuesMoyenne,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Graphique des totaux
        new Chart(document.getElementById('totalChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: labelsTotal,
                datasets: [{
                    label: 'Total des Dépenses (€)',
                    data: valuesTotal,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            }
        });

        // Graphique des utilisateurs
        new Chart(document.getElementById('userChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labelsUsers,
                datasets: [{
                    label: 'Nombre d\'Utilisateurs',
                    data: valuesUsers,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            }
        });
    </script>
</body>
</html>

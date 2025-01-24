<?php
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est un étudiant
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les informations de l'étudiant connecté
$id_etudiant = $_SESSION['user']['id']; // Supposant que l'ID utilisateur est stocké dans la session
$etudiant = null;

try {
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, etablissement, date_de_naissance 
        FROM etudiant 
        WHERE id = ?
    ");
    $stmt->execute([$id_etudiant]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f4f4f4;
            font-weight: bold;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background: #74ebd5;
            text-decoration: none;
            border-radius: 4px;
        }

        a:hover {
            background: #5fc2b7;
        }

        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Profil Étudiant</h1>
    <?php if ($etudiant): ?>
        <table>
            <tr>
                <th>Nom</th>
                <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
            </tr>
            <tr>
                <th>Établissement</th>
                <td><?php echo htmlspecialchars($etudiant['etablissement']); ?></td>
            </tr>
            <tr>
                <th>Date de Naissance</th>
                <td><?php echo htmlspecialchars($etudiant['date_de_naissance']); ?></td>
            </tr>
        </table>
        <a href="logout.php">Se déconnecter</a>
    <?php else: ?>
        <p class="error">Aucune information disponible pour cet étudiant.</p>
    <?php endif; ?>
</div>
</body>
</html>

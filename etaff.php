<?php
// Connexion à la base de données MySQL
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

// Créer une connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Définir l'encodage UTF-8
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Vérifier si un étudiant a été supprimé
if (isset($_GET['supprimer_id'])) {
    $id = $_GET['supprimer_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM etudiant WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header('Location: etaff.php');
            exit;
        }
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupérer tous les étudiants depuis la base de données
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, etablissement, date_de_naissance, date_creation FROM etudiant ORDER BY id DESC");
    $stmt->execute();
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erreur lors de la récupération des données : " . $e->getMessage();
    $etudiants = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des étudiants</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            box-sizing: border-box;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
            color: #333;
        }

        /* Table styles */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid white;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4db8cc ;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        .message {
            color: #f44336;
            text-align: center;
            font-size: 18px;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #e53935;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
</style>
</head>
<body>
<div class="container">
    <h1>Liste des étudiants</h1>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (empty($etudiants)): ?>
        <div class="message">Aucun étudiant ajouté pour le moment.</div>
    <?php else: ?>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Établissement</th>
                <th>Date de naissance</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($etudiants as $etudiant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
                    <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($etudiant['etablissement']); ?></td>
                    <td><?php echo htmlspecialchars($etudiant['date_de_naissance']); ?></td>
                    <td>
                        <a href="etaff.php?supprimer_id=<?php echo $etudiant['id']; ?>"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">
                            <button class="btn-delete">Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="etudia.php">Ajouter un autre étudiant</a></p>
    <div class="card">
        <a href="pr.php" class="logout-btn">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>
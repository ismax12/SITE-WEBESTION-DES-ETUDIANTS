<?php
session_start();

// Connexion à la base de données MySQL
$host = 'localhost';
$db = 'gestion_etudiants';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Traitement des suppressions spécifiques
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_note'])) {
        $id_note = intval($_POST['id_note']);
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = :id");
        $stmt->execute(['id' => $id_note]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['delete_absence'])) {
        $id_absence = intval($_POST['id_absence']);
        $stmt = $pdo->prepare("DELETE FROM absences WHERE id = :id");
        $stmt->execute(['id' => $id_absence]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Récupération des dernières notes
$stmt_notes = $pdo->query("
    SELECT n.*, e.nom, e.prenom 
    FROM notes n 
    JOIN etudiant e ON n.id_etudiant = e.id 
    ORDER BY n.date_creation DESC 
    LIMIT 10
");
$notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

// Récupération des dernières absences
$stmt_absences = $pdo->query("
    SELECT a.*, e.nom, e.prenom 
    FROM absences a 
    JOIN etudiant e ON a.id_etudiant = e.id 
    ORDER BY a.date_absence DESC 
    LIMIT 10
");
$absences = $stmt_absences->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableaux des Notes et Absences</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            padding: 20px;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #74ebd5;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
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
            text-align: center;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        form {
            margin: 0;
        }
    </style>
</head>

<body>
<div class="main-container">
    <!-- Tableau des dernières notes -->
    <div class="card">
        <h2>Dernières notes</h2>
        <table>
            <tr>
                <th>Étudiant</th>
                <th>Matière</th>
                <th>Note</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?php echo htmlspecialchars($note['nom'] . ' ' . $note['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($note['etablissement']); ?></td>
                    <td><?php echo htmlspecialchars($note['note']); ?>/20</td>
                    <td><?php echo date('d/m/Y', strtotime($note['date_creation'])); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_note" value="<?php echo $note['id']; ?>">
                            <button type="submit" name="delete_note" class="logout-btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Tableau des dernières absences -->
    <div class="card">
        <h2>Dernières absences</h2>
        <table>
            <tr>
                <th>Étudiant</th>
                <th>Date</th>
                <th>Matière</th>
                <th>Justifiée</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($absences as $absence): ?>
                <tr>
                    <td><?php echo htmlspecialchars($absence['nom'] . ' ' . $absence['prenom']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?></td>
                    <td><?php echo htmlspecialchars($absence['etablissement']); ?></td>
                    <td><?php echo $absence['justifie'] ? 'Oui' : 'Non'; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_absence" value="<?php echo $absence['id']; ?>">
                            <button type="submit" name="delete_absence" class="logout-btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <a href="pr.php" class="logout-btn">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>

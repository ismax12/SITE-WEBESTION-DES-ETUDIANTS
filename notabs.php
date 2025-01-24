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

// Traitement de l'ajout d'une note
if (isset($_POST['ajouter_note'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notes (id_etudiant, etablissement, note) VALUES (:id_etudiant, :etablissement, :note)");
        $stmt->execute([
            ':id_etudiant' => $_POST['etudiant'],
            ':etablissement' => $_POST['etablissement'],
            ':note' => $_POST['note']
        ]);
        $success_message = "Note ajoutée avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de l'ajout de la note : " . $e->getMessage();
    }
}

// Traitement de l'ajout d'une absence
if (isset($_POST['ajouter_absence'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO absences (id_etudiant, date_absence, etablissement, justifie, commentaire) 
                              VALUES (:id_etudiant, :date_absence, :etablissement, :justifie, :commentaire)");
        $stmt->execute([
            ':id_etudiant' => $_POST['etudiant_absence'],
            ':date_absence' => $_POST['date_absence'],
            ':etablissement' => $_POST['etablissement_absence'],
            ':justifie' => isset($_POST['justifie']) ? 1 : 0,
            ':commentaire' => $_POST['commentaire']
        ]);
        $success_message = "Absence enregistrée avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de l'enregistrement de l'absence : " . $e->getMessage();
    }
}

// Récupération de la liste des étudiants
try {
    $stmt = $pdo->query("SELECT id, nom, prenom FROM etudiant ORDER BY nom, prenom");
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des étudiants : " . $e->getMessage();
}

// Liste des etablissements (à adapter selon vos besoins)
$etablissement = ['EIDIA', 'EMADU', 'EBS', 'EPS', 'MED'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes et Absences</title>
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
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: white;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #74ebd5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            background-color: #4db8cc;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
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

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
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
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Formulaire d'ajout de note -->
    <div class="card">
        <h2>Ajouter une note</h2>
        <form method="POST">
            <div class="form-group">
                <label for="etudiant">Étudiant</label>
                <select name="etudiant" id="etudiant" required>
                    <option value="">Sélectionnez un étudiant</option>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <option value="<?php echo $etudiant['id']; ?>">
                            <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="etablissement">etablissement</label>
                <select name="etablissement" id="etablissement" required>
                    <option value="">Sélectionnez une etablissement</option>
                    <?php foreach ($etablissement as $etablissements): ?>
                        <option value="<?php echo $etablissements; ?>"><?php echo $etablissements; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="note">Note</label>
                <input type="number" name="note" id="note" step="0.01" min="0" max="20" required>
            </div>

            <button type="submit" name="ajouter_note">Ajouter la note</button>
        </form>
    </div>

    <!-- Formulaire d'ajout d'absence -->
    <div class="card">
        <h2>Enregistrer une absence</h2>
        <form method="POST">
            <div class="form-group">
                <label for="etudiant_absence">Étudiant</label>
                <select name="etudiant_absence" id="etudiant_absence" required>
                    <option value="">Sélectionnez un étudiant</option>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <option value="<?php echo $etudiant['id']; ?>">
                            <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_absence">Date de l'absence</label>
                <input type="date" name="date_absence" id="date_absence" required>
            </div>

            <div class="form-group">
                <label for="etablissement_absence">Établissement</label>
                <select name="etablissement_absence" id="etablissement_absence" required>
                    <option value="">Sélectionnez un établissement</option>
                    <?php foreach ($etablissement as $etablissements): ?>
                        <option value="<?php echo $etablissements; ?>"><?php echo $etablissements; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label>
                    <input type="checkbox" name="justifie"> Absence justifiée
                </label>
            </div>

            <div class="form-group">
                <label for="commentaire">Commentaire</label>
                <textarea name="commentaire" id="commentaire" rows="3"></textarea>
            </div>

            <button type="submit" name="ajouter_absence">Enregistrer l'absence</button>
        </form>
    </div>
    <div class="card">
        <a href="pr.php" class="logout-btn">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>
